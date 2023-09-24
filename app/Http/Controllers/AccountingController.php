<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateAccountRequest;
use App\Models\Accounting;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Accounting::all();
        return response()->json([
            'accounts' => $accounts
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAccountRequest $request)
    {
        $account = new Accounting();
        $account->name = $request->input('name');
        $account->parent_id = $request->input('parent_id');
        $account->account = $request->input('account');

        $prefix = '';

        if ($account->parent_id) {
            $parent = Accounting::find($account->parent_id);
            if ($parent) {
                $x = $parent->account;
                if ($x > 0) {
                    return response()->json([
                    'message' => "Error: You can't create a child account because the parent account has an account",
                    ], 400);
                } else {
                    $prefix = $parent->code . '.';
                }
            }
        }

        $account->save();
        $account->code = $prefix . $account->id;
        $account->update();
        $ancestors = $this->getAncestors($account->code);
        $response = [
            'message' => "Account created successfully",
            'account' => $account,
            'ancestors' => $ancestors,
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Accounting  $accounting
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account = Accounting::find($id);

        if (!$account) {
            return response()->json([
                'message' => "Account not found",
            ], 404);
        }

        $ancestors = $this->getAncestors($account->code);

        $response = [
            'message' => "Account founded successfully",
            'account' => $account,
            'ancestors' => $ancestors,
        ];
        return response()->json($response, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Accounting  $accounting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $account = Accounting::find($id);

        if (!$account) {
            return response()->json([
                'message' => "Account not found",
            ], 404);
        }
        $validator = $request->validate([
            'name' => 'required|string',
            'account' => 'required',
        ]);
        $account->name = $validator['name'];
        $account->account = $validator['account'];
        $account->update();
        if ($account) {
            return response()->json([
                'message' => "account Updated successfully",
                'account' => $account
            ], 200);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Accounting  $accounting
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = Accounting::find($id);
        if($account){
            $account->delete();
            return response()->json([
                'message' => 'accounting deleted successfully',
            ], 200);
        }

        return response()->json([
            'message' => "Error : account not found",
        ], 400);
    }

    private function getAncestors($code)
    {
        $ancestors = [];
        while ($code) {
            $code = $this->getParentCode($code);
            if ($code) {
                $account = Accounting::where('code', $code)->first();
                if ($account) {
                    $ancestors[] = $account;
                }
            }
        }
        return $ancestors;
    }

    private function getParentCode($code)
    {
        $lastDotPosition = strrpos($code, '.');
        if ($lastDotPosition === false) {
            return '';
        }
        return substr($code, 0, $lastDotPosition);
    }
}
