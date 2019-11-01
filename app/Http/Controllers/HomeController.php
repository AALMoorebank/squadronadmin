<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Member;
use App\ActiveKids;
use App\Roll;
use App\Rollmapping;
use App\Settings;
use Carbon\Carbon;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $rollweek = Carbon::now()->weekNumberInMonth;


       $activeroll = Rollmapping::latest()->value('id');

       $currentroll= DB::table('rolls')
       ->join('rollmappings', 'rolls.roll_id' , '=', 'rollmappings.id' )
       ->join('members', 'members.id', '=', 'rolls.member_id')
       ->join('rankmappings', 'members.rank', '=', 'rankmappings.id' )
       ->join('rollstatus', 'rolls.status', '=', 'status_id')
       ->Select('members.*', 'rolls.roll_id', 'rankmappings.*', 'rollstatus.status', 'rolls.status')
       ->where('rolls.roll_id', '=', $activeroll)
       ->where('rolls.status', '!=', 'A')
       ->orderby ('rankmappings.id')
       ->get();

        $members=Member::where('active', '=', 'Y')->get();
        $active=Activekids::all();
        $Roll=Roll::all();

        $subsfee=Settings::where('setting', '=', 'Weekly Fees')->value('value');

        $subs = DB::table('rolls')
            ->where('rolls.roll_id', '=', $activeroll)
            ->where('rolls.status', '=', 'C')
            ->count();

        $total = $subs*$subsfee;

            $officers = DB::table('rolls')
            ->join('rollmappings', 'rolls.roll_id' , '=', 'rollmappings.id' )
            ->join('members', 'members.id', '=', 'rolls.member_id')
            ->join('rankmappings', 'members.rank', '=', 'rankmappings.id' )
            ->join('rollstatus', 'rolls.status', '=', 'status_id')
            ->Select('members.*', 'rolls.roll_id', 'rankmappings.*', 'rollstatus.status', 'rolls.status')
            ->where('rolls.roll_id', '=', $activeroll)
            ->where('rolls.status', '!=', 'A')
            ->where('members.rank', '<', 12 )
            ->orderby ('rankmappings.id')
            ->get();


            $to = DB::table('rolls')
            ->join('rollmappings', 'rolls.roll_id' , '=', 'rollmappings.id' )
            ->join('members', 'members.id', '=', 'rolls.member_id')
            ->join('rankmappings', 'members.rank', '=', 'rankmappings.id' )
            ->join('rollstatus', 'rolls.status', '=', 'status_id')
            ->Select('members.*', 'rolls.roll_id', 'rankmappings.*', 'rollstatus.status', 'rolls.status')
            ->where('rolls.roll_id', '=', $activeroll)
            ->where('rolls.status', '!=', 'A')
            ->whereBetween('members.rank', [12,13])
            ->orderby ('rankmappings.id')
            ->get();


            $nco = DB::table('rolls')
            ->join('rollmappings', 'rolls.roll_id' , '=', 'rollmappings.id' )
            ->join('members', 'members.id', '=', 'rolls.member_id')
            ->join('rankmappings', 'members.rank', '=', 'rankmappings.id' )
            ->join('rollstatus', 'rolls.status', '=', 'status_id')
            ->Select('members.*', 'rolls.roll_id', 'rankmappings.*', 'rollstatus.status', 'rolls.status')
            ->where('rolls.roll_id', '=', $activeroll)
            ->where('rolls.status', '!=', 'A')
            ->whereBetween('members.rank', [14,18])
            ->orderby ('rankmappings.id')
            ->get();


            $cadet = DB::table('rolls')
            ->join('rollmappings', 'rolls.roll_id' , '=', 'rollmappings.id' )
            ->join('members', 'members.id', '=', 'rolls.member_id')
            ->join('rankmappings', 'members.rank', '=', 'rankmappings.id' )
            ->join('rollstatus', 'rolls.status', '=', 'status_id')
            ->Select('members.*', 'rolls.roll_id', 'rankmappings.*', 'rollstatus.status', 'rolls.status')
            ->where('rolls.roll_id', '=', $activeroll)
            ->where('rolls.status', '!=', 'A')
            ->where('members.rank', '>', 18 )
            ->orderby ('rankmappings.id')
            ->get();


            $count1 = Roll::whereHas('rollmapping', function ($query){
                $query->whereYear('roll_date', now()->year);
            })->count();

            $count2 = Roll::whereHas('rollmapping', function ($query) {
                $query->whereYear('roll_date', now()->year);
                })
                ->where ('status', '!=', 'A')
                ->count();

            $count3 = Roll::whereHas('rollmapping', function ($query) {
                $query->whereYear('roll_date', now()->year);
                })
                ->where ('status', '!=', 'A')
                ->where ('roll_id', '!=', $activeroll)
                ->count();

            if ($count1 == 0) {
                $avgattendance = 0;
            } else {

            $avgattendance = ($count2/$count1) * 100;
            }

            if ($count1 == 1) {
                $pastavg = $avgattendance;
            } else {
            $pastavg = ($count3/($count1-1)) * 100;
            }

            if($pastavg > $avgattendance){
                $tend = 0;
            }
            $tend = 1;

            $version = 2.1;

        return view('home', compact ('members', 'active', 'currentroll', 'total', 'officers', 'to', 'nco', 'cadet', 'rollweek', 'avgattendance', 'tend', 'version'));
    }
}
