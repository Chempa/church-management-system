@extends('layouts.app')

@section('title') Member Profile @endsection
@section('content')
<!--CONTENT CONTAINER-->
<!--===================================================-->
<div id="content-container">
        <div id="page-head">

                <!--Page Title-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <div id="page-title">
                        <h1 class="page-header text-overflow">Member</h1>
                </div>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--End page title-->


                <!--Breadcrumb-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <ol class="breadcrumb">
                  <li>
                      <i class="fa fa-home"></i><a href="{{route('dashboard')}}"> Dashboard</a>
                  </li>
                  <li>
                      <i class="fa fa-users"></i><a href="{{route('members.all')}}"> Members</a>
                  </li>
                        <li class="active">Profile</li>
                </ol>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--End breadcrumb-->

        </div>


        <!--Page content-->
        <!--===================================================-->
        <div id="page-content">
          <div class="col-md-12 col-md-offset-0 col-lg-8 col-lg-offset-1">
                <div class="panel">
                        <div class="panel-body">
                                <div class="fixed-fluid">
                                        <div class="fixed-md-400 pull-sm-left fixed-right-border">

                                                <!-- Simple profile -->
                                                <div class="text-center">
                                                        <div class="pad-ver">
                                                                <img src="{{url('/public/images/')}}/{{$member->photo}}" class="img-lg img-circle" alt="Profile Picture">
                                                        </div>
                                                        <h4 class="text-lg text-overflow mar-no">{{$member->title}}. {{$member->getFullname()}}</h4>
                                                        <p class="text-sm text-muted">{{$member->occupation}}</p>

                                                        <div class="pad-ver btn-groups">
                                                                <a href="app-profile.html#" class="btn btn-icon demo-pli-facebook icon-lg add-tooltip" data-original-title="Facebook" data-container="body"></a>
                                                                <a href="app-profile.html#" class="btn btn-icon demo-pli-twitter icon-lg add-tooltip" data-original-title="Twitter" data-container="body"></a>
                                                                <a href="app-profile.html#" class="btn btn-icon demo-pli-google-plus icon-lg add-tooltip" data-original-title="Google+" data-container="body"></a>
                                                                <a href="app-profile.html#" class="btn btn-icon demo-pli-instagram icon-lg add-tooltip" data-original-title="Instagram" data-container="body"></a>
                                                        </div>
                                                        <a href="tel:{{$member->phone}}" class="btn  btn-success btn-md">Call</a>
                                                        <a href="{{route('email')}}?mail={{$member->email}}" class="btn  btn-primary btn-md">Email</a>
                                                </div>
                                                <hr>

                                                <!-- Profile Details -->
                                                <p class="pad-ver text-main text-sm text-uppercase text-bold">Details</p>
                                                <p>
                                                        <i class="demo-pli-map-marker-2 icon-lg icon-fw"></i>{{$member->address}}</p>
                                                <p>
                                                        <a href="app-profile.html#" class="btn-link">
                                                                <i class="demo-pli-internet icon-lg icon-fw"></i>{{$member->email}}</a>
                                                </p>
                                                <p>
                                                        <i class="demo-pli-old-telephone icon-lg icon-fw"></i>{{$member->phone}}</p>
                                                        <p>
                                                        <i class="demo-pli-old-telephon icon-lg icon-fw"></i>{{$member->address}}</p>
                                                        <p>
                                                        <i class="demo-pli-old-house icon-lg icon-fw"></i>{{$member->city}}</p>
                                                        <p>
                                                        <i class="demo-pli-old-house icon-lg icon-fw"></i>{{$member->state}}</p>
                                                        <p>
                                                        <i class="demo-pli-old-teleph icon-lg icon-fw"></i>{{$member->country}}</p>
                                                <p class="text-sm text-center"></p>


                                                <hr>
                                                <p class="pad-ver text-main text-sm text-uppercase text-bold">Position</p>
                                                <ul class="list-inline">
                                                        <li class="tag tag-sm">{{$member->position}}</li>
                                                        <!--li class="tag tag-sm">Building Committee</li>
                                                        <li class="tag tag-sm">Usher</li>
                                                        <li class="tag tag-sm">Evangelism</li-->
                                                </ul>
                                                <hr>
                                                <p class="pad-ver text-main text-sm text-uppercase text-bold">Relatives</p>

                                                <?php if (!empty($member->relative) || strlen($member->relative)>0){ // do this only if there are relatives assigned to the member?>
                                                <?php $relatives = json_decode($member->relative); ?>

                                                <ol class="list-inline">
                                                <?php

                                                        foreach ($relatives as $relative){

                                                                $rel = App\Member::where('id',$relative->id)->get()->first();

                                                ?>

                                                                <li class="tag tag-sm"><a href="{{route('member.profile', $rel->id)}}">{{$rel->getFullname()}}</a> - {{$relative->relationship}}</li><br/>

                                                <?php
                                                        }
                                                } else {echo '<li class="tag tag-sm">No Relatives</li><br/>';}
                                                ?>
                                                </ol>

                                                <hr>
                                                <!--<p class="pad-ver text-main text-sm text-uppercase text-bold">Gallery</p>-->

                                        </div>
                                        <!--<h3>Attendance</h3>-->
                                        <div class="fixed-md-800 pull-sm-left fixed-right-border">

                                                <!-- Bar Chart -->
                                                <!---------------------------------->

                                                <div id="demo-morris-bar-month" style="height: 250px"></div>

                                                <!-- Bar Chart -->
                                                <!---------------------------------->

                                                    <div id="demo-morris-bar-month-collection" style="height: 250px"></div>

                                                <!---------------------------------->

                                                <!---------------------------------->

                                        </div>

                                </div>
                        </div>
                </div>
              </div>
        </div>
        <!--===================================================-->
        <!--End page content-->

</div>
<!--===================================================-->
<!--END CONTENT CONTAINER-->

@endsection
