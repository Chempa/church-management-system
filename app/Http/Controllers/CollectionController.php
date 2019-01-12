<?php

namespace App\Http\Controllers;

use App\Collection;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = \Auth::user();
        $members = \App\Member::where('branch_id', $user->branchcode)->get();
        $services = $user->getServiceTypes();
        $collections = $user->getCollectionTypes();
        $currency = \App\Options::where('name', 'currency')->first();
        $currency = DB::table('country')->where('currency_symbol', $currency->value)->first();
        return view('collection.offering', compact('members', 'services', 'collections', 'currency'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $branch = \Auth::user();
      // validate date
      $split_date_array = explode("-",date('Y-m-d',strtotime($request->get('date_collected'))));
      if (Carbon::createFromDate($split_date_array[0], $split_date_array[1], $split_date_array[2])->isFuture())
      {
          return response()->json(['status' => false, 'text' => "**You can't save collection for a future date!"]);
      }
      // check if collection has already been saved for that date
      $savings = \App\Savings::getByDate($branch, $request->get('date_collected'));
      if ($savings > 0){
          return response()->json(['status' => false, 'text' => "**Branch Collection for {$this->get_date_in_words(date('Y-m-d',strtotime($request->get('date_collected'))))} has been saved before!"]);
      }

      $c_type = \App\CollectionsType::all();
      foreach ($c_type as $key => $type) {
        // code...
        $name = $type->name;
        $savings = \App\Savings::create([
          'branch_id' => $branch->id,
          'collections_types_id' => $type->id,
          'service_types_id' => $request->type,
          'amount' => $request->$name,
          'date_collected' => date('Y-m-d',strtotime($request->date_collected))
        ]);
      }
      return response()->json(['status' => true, 'text' => 'Branch Collection Successfully Saved']);
    }

    public function member(Request $request){
      $branch = \Auth::user();
      // validate date
      $split_date_array = explode("-",date('Y-m-d',strtotime($request->get('date_collected'))));
      if (Carbon::createFromDate($split_date_array[0], $split_date_array[1], $split_date_array[2])->isFuture())
      {
          return response()->json(['status' => false, 'text' => "**You can't save collection for a future date!"]);
      }
      // check if collection has already been saved for that date
      $savings = \App\MemberSavings::getByDate($branch, $request->get('date_collected'));
      if ($savings > 0){
          return response()->json(['status' => false, 'text' => "**Member Collection for {$this->get_date_in_words(date('Y-m-d',strtotime($request->get('date_collected'))))} has been saved before!"]);
      }

      $c_type = \App\CollectionsType::all();
      foreach ($c_type as $key => $type) {
        // code...
        $name = $type->name;
        for($i = 0; $i < count($request['member_id']); $i++){
          $savings = \App\MemberSavings::create([
            'branch_id' => $branch->id,
            'member_id' => $request['member_id'][$i],
            'collections_types_id' => $type->id,
            'service_types_id' => $request->type,
            'amount' => $request->$name[$i],
            'date_collected' => date('Y-m-d',strtotime($request->date_collected))
          ]);
        }
      }

      return response()->json(['status' => true, 'text' => 'Member Collection Successfully Saved']);

      $user = \Auth::user();

      $split_date_array = explode("-",date('Y-m-d',strtotime($request->get('date'))));
      if (Carbon::createFromDate($split_date_array[0], $split_date_array[1], $split_date_array[2])->isFuture())
      {
          return response()->json(['status' => false, 'text' => "**You can't save collection for a future date!"]);
      }

      // check if collectio has already been marked for that date
      $attendance = DB::table('members_collection')->where('date_added', date('Y-m-d',strtotime($request->get('date'))) )->where('branch_id',$user->branchcode )->get(['id'])->count();
      if ($attendance > 0){
          return response()->json(['status' => false, 'text' => "**Member Collection for {$this->get_date_in_words(date('Y-m-d',strtotime($request->get('date'))))} has been saved before!"]);
      }

      $offer = $request;
      for($i = 0; $i < count($offer['member_id']); $i++) {
        // code...
        $value = [
        'member_id' => $offer['member_id'][$i],
        'title' => $offer['title'][$i],
        'fname' => $offer['fname'][$i],
        'lname' => $offer['lname'][$i],
        'special_offering' => $offer['special_offering'][$i],
        'seed_offering' => $offer['seed_offering'][$i],
        'date_added' => date('Y-m-d',strtotime($offer['date'])),
        'offering' => $offer['offering'][$i],
        'donation' => $offer['donation'][$i],
        'tithe' => $offer['tithe'][$i],
        'first_fruit' => $offer['first_fruit'][$i],
        'covenant_seed' => $offer['covenant_seed'][$i],
        'love_seed' => $offer['love_seed'][$i],
        'sacrifice' => $offer['sacrifice'][$i],
        'thanksgiving' => $offer['thanksgiving'][$i],
        'thanksgiving_seed' => $offer['thanksgiving_seed'][$i],
        'other' => $offer['other'][$i],
        'branch_id' => $offer['branch_id'][$i],
        'date_submitted' => now(),
        'service_type' => $offer['type'],
        ];
            DB::table('members_collection')->insert($value);
      }

      return response()->json(['status' => true, 'text' => 'Member Collection Successfully Saved']);
      // return redirect()->back()->with(['success' => 'Successful']);
    }

    /**
     * Show Collection report.
     *
     * @param  \App\Collection  $collection
     * @return \Illuminate\Http\Response
     */
    public function report()
    {
      $code = \Auth::user()->branchcode;
      $user = \Auth::user();
      $c_types = $user->getCollectionTypes();
      \App\CollectionsType::disFormatStringAll($c_types);
      return view('collection.report', compact('c_types'));
    }

    private function get_date_in_words($date)
    {
        $split_date_array = explode("-",$date);
        return Carbon::createFromDate($split_date_array[0], $split_date_array[1], $split_date_array[2])->format('l, jS \\of F Y');
    }

    public function calculateTotal($savings, $type = false){
      $total = 0;
      foreach ($savings as $key => $value) {
        if ($type == 'now') {
          if ($value->date_collected ==  now()->toDateString() ) {
            $total += array_sum($value->amounts);
          }
        } else {
          $total += array_sum($value->amounts);
        }
      }
      return $total;
    }

    public function calculateSingleTotal($savings, $type){
      $obj = [];
      foreach ($savings as $key => $value) {
        switch ($type) {
          case 'day': $t = 'D'; break;
          case 'week': $t = 'W'; break;
          case 'month': $t = 'M'; break;
          case 'year': $t = 'Y'; break;
        }
        $date = date($t, strtotime($value->date_collected));
        $year = (int)substr($value->date_collected, 0,4);
        foreach ($value->amounts as $ke => $valu) {
          if (isset($obj[$date])) {
            if (isset($obj[$date]->$ke)) {  $obj[$date]->$ke += $valu; } else { $obj[$date]->$ke = $valu; }
          } else {
            $obj[$date] = new \stdClass();
            $obj[$date]->$ke = $valu;
            $obj[$date]->$type = $date;
          }
        }
      }
      return $obj;
    }

    public function analysis()
    {
      $user = \Auth::user();
      $savings = \App\Savings::rowToColumn(\App\Savings::where('branch_id', $user->id)->get());
      $mSavings = \App\MemberSavings::rowToColumn(\App\MemberSavings::where('branch_id', $user->id)->get());
      $c_types = \App\CollectionsType::getTypes();

      $collections = $this->calculateSingleTotal($savings, 'month');
      $collections2 = $this->calculateSingleTotal($savings, 'day');
      $collections3 = $this->calculateSingleTotal($savings, 'week');
      $collections4 = $this->calculateSingleTotal($savings, 'year');

      return view('collection.analysis', compact('collections','collections2','collections3','collections4', 'c_types'));
    }

    public function yData($collection,$c_types, $value){
      $y = new \stdClass();
      $y->y = $value;  $i = 1; $size = sizeof($c_types);
      foreach ($c_types as $key => $value) {
        $name = $value->name;
        $amount = isset($collection->$name) ? $collection->$name : 0;
        $y->$name = $amount;
        $i++;
      }
      return $y; //. "},";
    }

    public function noData($c_types, $value){
        $y = new \stdClass();
        $y->y = $value; $i=1;
        foreach ($c_types as $key => $value) {
          $name = $value->name;
          $y->$name = 0;
          $i++;
        }
        return $y;//. "},";
      }

    public function test (Request $request){
      $user = \Auth::user();
      $c_types = \App\CollectionsType::getTypes();
      $savings = \App\Savings::rowToColumn(\App\Savings::where('branch_id', $user->id)->get());
      $interval = $request->interval;
      $group = $request->group;
      $months = [];
      for ($i = $interval-1; $i >= 0; $i--) {
        $t = 'M';
        switch ($group) {
          case 'day': $t = 'D'; break;
          case 'week': $t = 'W'; break;
          case 'month': $t = 'M'; break;
          case 'year': $t = 'Y'; break;
        }
        $dateOrNot = $group == 'month' ? date('Y-m-01') : '';
        $months[$i] = date($t, strtotime($dateOrNot. "-$i $group")); //1 week ago
      }
      $collections2 = $this->calculateSingleTotal($savings, $group);
      $dt = (function($savings, $c_types, $months, $group){
        $output = [];
        foreach ($months as $key => $value) {
    		$month = $value; $found = false;
    		foreach ($savings as $collection) {
    			if($value == $collection->$group){
    				$found = true;
            $output[] = $this->yData($collection, $c_types, $value);
    			}
    		}
    		if(!$found){
    			$output[] = $this->noData($c_types, $value);
            //"', 1: 0, 2: 0, 3: 0, 4: 0, 5: 0},";
    		}

    	}return $output;})($collections2, $c_types, $months, $group);
      // dd($dt);
      // Get the number of days to show data for, with a default of 7

      // $range = Carbon::now()->subDays('$days');
      //
      // $stats = Collection::
      //   where('created_at', '>=', $range)
      //   ->where('branch_id', $user->branchcode)
      //   ->groupBy('day', 'date_collected')
      //   ->orderBy('date_collected', 'ASC')
      //   ->get([
      //     DB::raw('Date(date_collected) as date'),
      //     DB::raw('DAYNAME(date_collected) AS day'),
      //     DB::raw('COUNT(*) as value')
      //   ]);

      return response()->json($dt);
    }

  public function history(Request $request){
    $branch = \Auth::user();
    $history = collect(new \App\Savings);//[];
    if (isset($request->branch)) {
      // code...
      $history = \App\Savings::rowToColumn(\App\Savings::where('branch_id', $branch->id)
      ->with('collections_types')->with('service_types')->get());
    }
    if(isset($request->member)) {
      $history = \App\MemberSavings::rowToColumn(\App\MemberSavings::where('branch_id', $branch->id)
      ->with('member')->with('collections_types')->with('service_types')->get());

    }
    return Datatables::of($history)->make(true);
  }
}
