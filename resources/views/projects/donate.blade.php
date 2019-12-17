<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CCS - Donate {{$project->title}}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="/meta/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/meta/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/meta/favicon-16x16.png">
    <link rel="manifest" href="/meta/manifest.json">
    <link rel="mask-icon" href="/meta/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#ffffff">

    <link href="/css/custom.css" rel="stylesheet">


    <meta name="msapplication-config" content="/ietemplates/ieconfig.xml">

</head>


<body>
<div class="page-wrapper">

    <div class="mob-nav">
              <input class="burger-check" id="mobile-burger" type="checkbox"><label for="mobile-burger" class="burger"></label>
           
        <div class="slide-in-nav">
             <div class="container slide-in">
                 <div class="row">
                     <div class="col-xs-12"> 
                         <div class="text-center nav-item mob">
                            <a href="/ideas/" class="top-link">Ideas</a>
                         </div>
                         <div class="text-center nav-item mob">
                            <a href="/funding-required/">Funding Required</a>
                         </div>
                         <div class="text-center nav-item mob">
                            <a href="/work-in-progress/">Work in Progress</a>
                         </div>
                         <div class="text-center nav-item mob">
                            <a href="/completed-proposals/">Completed Tasks</a>
                         </div>
                         <div class="text-center nav-item mob">
                            <a href="/donate/index.html">Donate</a>
                         </div>
                         <div class="text-center nav-item mob">
                            <a href="/completed-proposals/">Back to Getmonero.org</a>
                         </div>
                     </div>                     
                 </div>          
             </div>
          
        </div>
       </div>
       
        <div class="desktop-nav">
           <nav class="container">
              <div class="row middle-xs">
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <p class="site-name"><a href="/">Community Crowdfunding System</a></p>
                  </div>
                  <div class="col-lg-8 col-md-8 col-sm-8 items end-xs">
                      <div class="row end-xs middle-xs">
                         <div class="col-md-12">
                            <div class="dropdown">
                                <label for="desktopdrop">CCS Stages<div class="arrow-down"></div></label>
                                <input class="burger-checkdropdown" id="desktopdrop" type="checkbox">
                                <div class="dropdown-content">
                                      <a href="/ideas/">Ideas</a>
                                      <a href="/funding-required/">Funding Required</a>
                                      <a href="/work-in-progress/">Work in Progress</a>
                                      <a href="/completed-proposals/">Completed Tasks</a>
                                </div>
                            </div>  
                            <a href="https://getmonero.org">Getmonero.org</a>
                            <a href="/donate/" class="donate-btn">Donate</a>
                          </div>
                      </div>
                  </div>
              </div>
        </nav>
        </div>
        <div class="mob bot-nav white-nav">
            <div class="row middle-xs">
                <div class="col-xs-12">
                    <p class="site-name"><a href="
                    
                    /
                    
                    ">Community Crowdfunding System</a></p>
                </div>
            </div>     
        </div>

    <div class="site-wrap ffs-proposal ffs-con">

        <div class="container ffs-breadcrumbs">
            <div class="row">
                <div class="col-xs-12">
                    <p><a href="/">Community Crowdfunding System</a></p>
                    <p><a href="/funding-required/">Funding Required</a></p>
                    <p><a href="/proposals/{{pathinfo($project->filename, PATHINFO_FILENAME)}}.html">{{$project->title}}</a></p>
                    <p class="bread-active">Contribute</p>
                </div>
            </div>
        </div>

        <section class="container full">
            <div class="info-block">
                <div class="row">
                    <div class="col-xs-12"> 
                        <h2>{{$project->title}}</h2>
                            <p class="author-list">{{$project->author}}</p>
                            <p class="date-list">{{date('F j, Y', strtotime($project->created_at))}}</p>
                            <p class="date-list contributor">{{$project->contributions}}</p>
                            <p class="bar-fund-status">Raised <span class="progress-number-funded">{{$project->raised_amount}}</span> of <span class="progress-number-goal">{{$project->target_amount}}</span> XMR</p>
                        <div class="progress-bar">
                            <span class="fund-progress" style="width: {{min(100, intval($project->raised_amount * 100 / $project->target_amount))}}%"></span>
                        </div>
                        <p>Your contribution should be visible within 5 minutes of you sending your contribution. If for some reason it is not there, please contact a member of the Core Team!</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="container full">
            <div class="row">
                <div class="col-xs-12">
                    <div class="tabs con-how">
                        <input class="input" name="tabs" type="radio" id="tab-1" checked="checked"/>
                        <label class="label" for="tab-1">QR Code</label>
                        <div class="panel col-xs-12">
                            <div class="panel-segment">
                                <h3>Contribute with a QR code</h3>
                                <p>1. Choose the amount of XMR you wish to contribute to this proposal</p>
                                <p>2. Scan this QR code or tap to open in your Monero wallet app:</p>
                                <p>
                                    <a href="{{$project->address_uri}}" class="qr"><img src="{{$project->qrCodeSrc}}"/></a>
                                </p>
                                <p>3. Send! Thank you! You are amazing!</p>
                            </div>
                        </div>
                        <input class="input" name="tabs" type="radio" id="tab-2"/>
                        <label class="label" for="tab-2">Address</label>
                        <div class="panel col-xs-12">
                            <div class="panel-segment">
                                <h3>Contribute using an address</h3>
                                <p>1. Choose the amount of XMR you wish to contribute to this proposal</p>
                                <p>2. Enter the following XMR address:</p>   <p class="string">{{$project->address}}</p>
                                <p>3. Send! Thank you! You are amazing!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="container-fluid">
            <div class="container">
                <div class="row center-xs footer-wrapper">
                     <div class="col-md-8 col-sm-10 col-xs-12">
                        <h3>Donate to the Monero Project</h3>
                        <p>By donating to the following Monero address (General Fund), you are supporting the Monero Project. If you wish to donate to a specific proposal, please see <a href="/funding-required/index.html" class="white gf">Funding Required</a>.</p>  
                        <p><a href="monero:44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A" class="qr"><img src="/img/donate-monero.png" /></a></p>
                        <p class="gf-address">44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A</p>
                     </div>
                </div>
            </div>
            <div class="row center-xs">
                    <div class="footer-links">
                        <ul class="list-unstyled list-inline">
                             <li><a href="https://repo.getmonero.org/monero-project/ccs-front" class="white footer-link">CCS Front End Repo</a></li>
                             <li><a href="https://repo.getmonero.org/monero-project/ccs-back" class="white footer-link">CCS Backend Repo</a></li>
                             <li><a href="https://repo.getmonero.org/monero-project/ccs-proposals" class="white footer-link">CCS Proposals Repo</a></li>
                        </ul>
                    </div>
            </div>
</footer>
</div>
</body>
</html>