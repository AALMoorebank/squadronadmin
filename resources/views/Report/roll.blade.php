<html>
    <head>
        <style>
            table, th, td {
                border: 1px solid black;
            }
            .text-center {
                text-align: center;
            }
            #leftbox {
                float: left;
                width: 30%;
                height: 20px;
            }

            #middlebox {
                float: left;
                width: 30%;
                height: 20px;
            }

            #rightbox {
                float: right;
                width: 30%;
                height: 20px;
            }

        </style>
    </head>
    <body>
        <div class="text-center">
            <img src="{{ asset('img/aal_logo.jpg') }}" height="90" class="text-center">
        </div>
        <h1 style="text-align:center">Roll of: {{date("l - jS F Y",strtotime($rolldate))}}</h1>
             <p style="font-weight: bold">From:</p>
              Australian Air League Inc.<br>
              {{ config('app.name', 'Squadron') }} Sqaudron<br>

        <div id = "leftbox">
            <h4>Number on Roll: {{$strength}}</h4>
        </div>
        <div id = "middlebox">
            <h4>Number Present: {{$present}}</h4>
        </div>
        <div id = "rightbox">
            @if($strength != 0)
                <h4>Attendance %: {{number_format(($present/$strength)*100,2)}}%</h4>
            @else
                <h4 class="card-title">Attendance %: N/A</h4>
            @endif
        </div>
        <br>
        <br>
        <br>
        <br>

        <h3 style="text-align: center">Members Present</h3>
        <table style = "width:100%">
            <tr>
                <th class="text-center">Last Name</th>
                <th class="text-center">First Name</th>
                <th class="text-center">Membershtip Number</th>
                <th class="text-center">Rank</th>
            </tr>

            @foreach($members as $m)
                @if($m->status != 'A')
                    <tr>
                        <td class="text-center">{{$m->member->last_name}}</td>
                        <td class="text-center">{{$m->member->first_name}}</td>
                        <td class="text-center">{{$m->member->membership_number}}</td>
                        <td class="text-center">{{$m->member->memberrank->rank}}</td>
                    </tr>
                @endif
            @endforeach
        </table>
        <br>
        <br>
        <h3 style="text-align: center">Members not Present</h3>
        <table style = "width:100%; border: 1px black;">
            <tr>
                <th class="text-center">Last Name</th>
                <th class="text-center">First Name</th>
                <th class="text-center">Membershtip Number</th>
                <th class="text-center">Rank</th>
            </tr>

            @foreach($members as $m)
                @if($m->status == 'A')
                    <tr>
                        <td class="text-center">{{$m->member->last_name}}</td>
                        <td class="text-center">{{$m->member->first_name}}</td>
                        <td class="text-center">{{$m->member->membership_number}}</td>
                        <td class="text-center">{{$m->member->memberrank->rank}}</td>
                    </tr>
                @endif
            @endforeach
        </table>

        <br>
        <br>
        <br>

    <small style="text-align: center">This roll report was generated on {{date("l - jS F Y",strtotime($reportdate))}} at {{date("g:i a")}}</small><br>
    <small> Report created by {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</small>
    </body>
</html>

