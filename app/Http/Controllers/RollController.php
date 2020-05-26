<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Alert;

use App\Member;
use App\Roll;
use App\Rollmapping;
use App\RollStatus;
use App\Accounts;

class RollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        //
        $rollid = Rollmapping::latest()->value('id');
        $rolldate = Rollmapping::latest()->value('roll_date');



        $members = Roll::with(array('member' => function($q) {
            return $q->orderby('rank');
        }))
        ->where('roll_id', '=', $rollid)->orderby('status')->get();

        return view('roll.index', compact('members', 'rolldate', 'rollid'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function parade()
    {
        $rollid = Rollmapping::latest()->value('id');
        $rolldate = Rollmapping::latest()->value('roll_date');

        $fparade = Roll::with(['Member' => function ($q) {
            $q->orderby('rank');
        }])
        ->where('status', '!=', 'A')->where('roll_id', '=', $rollid)->get();

        return view('roll.first', compact('rolldate', 'fparade'));
    }



     public function create()
    {
        //
        return view('roll.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = Validator::make($request->all(),[
            'rolldate' => 'required'
        ]);

        if ($validateData ->fails())
        {
            return Redirect::back()->withErrors($validateData)->withInput();
        }

        //Create Rollmapping
        $date = Carbon::parse($request->get('rolldate'))->format('Y-m-d');
        $e = new Rollmapping();
        $e->roll_date = Carbon::parse($request->get('rolldate'));
        $e->roll_year = Carbon::parse($date)->year;
        $e->roll_month = Carbon::parse($date)->month;
        $e->roll_week = Carbon::parse($date)->weekOfMonth;
        $e->save();

        //create Roll
        $rollid = Rollmapping::latest()->value('id');

        $members = Member::where('active', '=', 'Y')->Where('member_type', '=', 'League')->orderBy('rank','asc')->get();

            foreach ($members as $m)
            {
                $r=new Roll;
                $r->member_id = $m->id;
                $r->roll_id = $rollid;
                $r->Status = 'A';
                $r->save();
            }
        Alert::success('New Roll Created')->autoclose(1500);
       return redirect(action('RollController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function paid($id)
    {

        $r = Roll::find($id);
        $rollid = Rollmapping::latest()->value('id');

        if ($r != null)
        {
            $r->status = "C";
            $r->paidrollid = $rollid;
            $r->save();

            Alert::Success('Member Paid', 'Member Paid Cash')->autoclose(1500);
            return redirect(action('RollController@index'));
        }

        return redirect(action('RollController@index'));
    }



    public function voucher($id)
    {
        $r = Roll::find($id);
        $rollid = Rollmapping::latest()->value('id');
        $rolldate = Rollmapping::latest()->value('roll_date');

        if ($r != null)
        {
            // Check if ActiveKids Balance is not less than 0
            if ($r->member->Accounts->sum('amount') >= 10)
            {
                // Update Roll Status
                $r->status = "V";
                $r->paidrollid = $rollid;
                $r->save();

                // Insert Record into ActiveKids Voucher
                $voucher = new Accounts();
                $voucher->member_id = $r->member_id;
                $voucher->Reason = 'Weekly Subs';
                $voucher->amount = -10;
                $voucher->save();

                Alert::Success("Paid", "Member paid from Account Balance")->autoclose(1500);
                return redirect(action('RollController@index'));
            }
            else
            {
                //Not Enough money in the account
                Alert::error("Error", "Insufficient Account Balance")->autoclose(1500);
                return redirect(action('RollController@index'));
            }
        }

        return redirect(action('RollController@index'));
    }

    public function notpaid($id)
    {
        $r = Roll::find($id);

        if ($r != null)
        {
            $r->status = "P";
            $r->save();

            alert::success('Member Present', 'Member has not paid')->autoclose(1500);
            return redirect(action('RollController@index'));
        }

        return redirect(action('RollController@index'));
    }

    public function updateRoll($id)
    {
        $o = Roll::find($id);
        $rollid = Rollmapping::latest()->value('id');

        if ($o != null)
        {
            $o->status = "C";
            $o->paidrollid = $rollid;
            $o->save();

            alert::success('Member Paid', 'Past Sub has been marked as paid')->autoclose(2000);
           return redirect(action('MembersController@show', $o->member_id));
        }

        return redirect(action('MembersController@index'));
    }
}
