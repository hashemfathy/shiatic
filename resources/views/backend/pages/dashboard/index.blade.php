@extends('backend.welcome')
@section('title')
Dashboard
@endsection
<style media="screen">
    .tile {
        width: 100%;
        background: #fff;
        border-radius: 5px;
        box-shadow: 0px 2px 3px -1px rgba(151, 171, 187, 0.7);
        float: left;
        transform-style: preserve-3d;
        margin: 10px 5px;

    }

    .header {
        border-bottom: 1px solid #ebeff2;
        padding: 19px 0;
        text-align: center;
        color: #59687f;
        font-size: 600;
        font-size: 19px;
        position: relative;
    }

    .banner-img {
        padding: 5px 5px 0;
    }

    .banner-img img {
        width: 100%;
        border-radius: 5px;
    }

    .dates {
        border: 1px solid #ebeff2;
        border-radius: 5px;
        padding: 20px 0px;
        margin: 10px 20px;
        font-size: 16px;
        color: #5aadef;
        font-weight: 600;
        overflow: auto;
    }

    .dates div {
        width: 100%;
        text-align: center;
        position: relative;
    }

    .dates strong,
    .stats strong {
        display: block;
        color: #adb8c2;
        font-size: 11px;
        font-weight: 700;
    }

    .dates span {
        width: 1px;
        height: 40px;
        position: absolute;
        right: 0;
        top: 0;
        background: #ebeff2;
    }

    .stats {
        border-top: 1px solid #ebeff2;
        background: #f7f8fa;
        overflow: auto;
        padding: 15px 0;
        font-size: 16px;
        color: #59687f;
        font-weight: 600;
        border-radius: 0 0 5px 5px;
    }

    .stats div {
        border-right: 1px solid #ebeff2;
        width: 50%;
        float: left;
        text-align: center
    }

    .stats div:nth-of-type(3) {
        border: none;
    }

    div.footer {
        text-align: right;
        position: relative;
        margin: 20px 5px;
    }

    div.footer a.Cbtn {
        padding: 10px 25px;
        background-color: #DADADA;
        color: #666;
        margin: 10px 2px;
        text-transform: uppercase;
        font-weight: bold;
        text-decoration: none;
        border-radius: 3px;
    }

    div.footer a.Cbtn-primary {
        background-color: #5AADF2;
        color: #FFF;
    }

    div.footer a.Cbtn-primary:hover {
        background-color: #7dbef5;
    }

    div.footer a.Cbtn-danger {
        background-color: #fc5a5a;
        color: #FFF;
    }

    div.footer a.Cbtn-danger:hover {
        background-color: #fd7676;
    }

    .analytic {
        background-color: #59cdcb5c;
        padding: 14px;
        border: 2px solid white;
        text-align: center;
    }
</style>
@section('content')
<default-container>
    <b-row>
        <b-col style="flex: 0 0 100%;max-width: 100%;">
            <div class="brand-card">
                <div class="brand-card-header bg-facebook">
                    <h4>Clients</h4>
                    <div class="chart-wrapper">
                    </div>
                </div>
                <div class="brand-card-body">
                    <div>
                        <div class="text-value">{{$totalClientCount}}</div>
                        <div class="text-uppercase text-muted small">Total</div>
                    </div>
                    <div>
                        <div class="text-value">{{$maleClientCount}}</div>
                        <div class="text-uppercase text-muted small">Male</div>
                    </div>
                    <div>
                        <div class="text-value">{{$femaleClientCount}}</div>
                        <div class="text-uppercase text-muted small">Female</div>
                    </div>
                </div>
            </div>
        </b-col>
        <b-col style="flex: 0 0 100%;max-width: 100%;" sm="6" lg="3">
            <div class="brand-card">
                <div class="brand-card-header bg-twitter">
                    <h4>Total Visits</h4>
                    <div class="chart-wrapper">
                    </div>
                </div>
                <div class="brand-card-body">
                    <div>
                        <div class="text-value">{{$visitsCount['totalVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Total</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['ahmedAdelVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">C.ahmed adel</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['hanyVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Hany</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['HussienVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Hussien</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['ezzatVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Ezzat</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['omarVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Omar</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['baherVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Baher</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['mohamedAdelVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Mohamed adel</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['narimanVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Nariman</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['alaaVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Alaa</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['yaraVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Yara</div>
                    </div>
                    <div>
                        <div class="text-value">{{$visitsCount['unselectedVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Unselected</div>
                    </div>
                </div>
            </div>
        </b-col>
        <b-col style="flex: 0 0 100%;max-width: 100%;" sm="6" lg="3">
            <div class="brand-card">
                <div class="brand-card-header bg-google-plus">
                    <h4>Total Month Visits</h4>
                    <div class="chart-wrapper">
                    </div>
                </div>
                <div class="brand-card-body">
                    <div>
                        <div class="text-value">{{$monthVisitsCount['totalVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Total</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['ahmedAdelVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">C.ahmed adel</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['hanyVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Hany</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['HussienVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Hussien</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['ezzatVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Ezzat</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['omarVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Omar</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['baherVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Baher</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['mohamedAdelVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Mohamed adel</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['narimanVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Nariman</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['alaaVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Alaa</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['yaraVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Yara</div>
                    </div>
                    <div>
                        <div class="text-value">{{$monthVisitsCount['unselectedVisitsCount']}}</div>
                        <div class="text-uppercase text-muted small">Unselected</div>
                    </div>
                </div>
            </div>
        </b-col>
    </b-row>
    <!-- <b-row>
        <b-col style="flex: 0 0 50%;max-width: 50%;">
            <b-card header="Visits">
                <div class="chart-wrapper">
                    <bar-example :statics="{{json_encode($statics)}}" chartId="chart-bar-01" />
                </div>
            </b-card>
        </b-col>
    </b-row> -->
</default-container>
@endsection