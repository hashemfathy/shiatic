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
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <div class="tile">
                <div class="wrapper">
                    <div class="header">Clients</div>
                    <div class="banner-img">
                        <img src="/images/shiatic-logo-small.png" alt="Image 1">
                    </div>
                    <div class="dates">
                        <div class="start">
                            <strong>Total</strong> {{$totalClientCount}}
                        </div>
                    </div>
                    <div class="stats">
                        <div>
                            <strong>Male</strong> {{$maleClientCount}}
                        </div>
                        <div>
                            <strong>Female</strong> {{$femaleClientCount}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <div class="tile">
                <div class="wrapper">
                    <div class="header">Total Visits</div>
                    <div class="banner-img">
                        <img src="/images/shiatic-logo-small.png" alt="Image 1">
                    </div>
                    <div class="dates">
                        <div class="start">
                            <strong>Total</strong> {{$visitsCount['totalVisitsCount']}}
                        </div>
                    </div>
                    <div class="stats">
                        <div>
                            <strong>C.ahmed adel</strong> {{$visitsCount['ahmedAdelVisitsCount']}}
                        </div>
                        <div>
                            <strong>Hany</strong> {{$visitsCount['hanyVisitsCount']}}
                        </div>
                        <div>
                            <strong>Hussien</strong> {{$visitsCount['HussienVisitsCount']}}
                        </div>
                        <div>
                            <strong>Ezzat</strong> {{$visitsCount['ezzatVisitsCount']}}
                        </div>
                        <div>
                            <strong>Omar</strong> {{$visitsCount['omarVisitsCount']}}
                        </div>
                        <div>
                            <strong>Nariman</strong> {{$visitsCount['narimanVisitsCount']}}
                        </div>
                        <div>
                            <strong>Alaa</strong> {{$visitsCount['alaaVisitsCount']}}
                        </div>
                        <div>
                            <strong>Yara</strong> {{$visitsCount['yaraVisitsCount']}}
                        </div>
                        <div>
                            <strong>Unselected</strong> {{$visitsCount['unselectedVisitsCount']}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
            <div class="tile">
                <div class="wrapper">
                    <div class="header">Total Month Visits</div>
                    <div class="banner-img">
                        <img src="/images/shiatic-logo-small.png" alt="Image 1">
                    </div>
                    <div class="dates">
                        <div class="start">
                            <strong>Total</strong> {{$monthVisitsCount['totalVisitsCount']}}
                        </div>
                    </div>
                    <div class="stats">
                        <div>
                            <strong>C.ahmed adel</strong> {{$monthVisitsCount['ahmedAdelVisitsCount']}}
                        </div>
                        <div>
                            <strong>Hany</strong> {{$monthVisitsCount['hanyVisitsCount']}}
                        </div>
                        <div>
                            <strong>Hussien</strong> {{$monthVisitsCount['HussienVisitsCount']}}
                        </div>
                        <div>
                            <strong>Ezzat</strong> {{$monthVisitsCount['ezzatVisitsCount']}}
                        </div>
                        <div>
                            <strong>Omar</strong> {{$monthVisitsCount['omarVisitsCount']}}
                        </div>
                        <div>
                            <strong>Nariman</strong> {{$monthVisitsCount['narimanVisitsCount']}}
                        </div>
                        <div>
                            <strong>Alaa</strong> {{$monthVisitsCount['alaaVisitsCount']}}
                        </div>
                        <div>
                            <strong>Yara</strong> {{$monthVisitsCount['yaraVisitsCount']}}
                        </div>
                        <div>
                            <strong>Unselected</strong> {{$monthVisitsCount['unselectedVisitsCount']}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</default-container>
@endsection