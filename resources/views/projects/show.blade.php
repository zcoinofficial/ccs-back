XMR {{$project->raised_amount}} /  XMR {{$project->target_amount}} Target

{{$project->contributions}} contributions made.  {{$project->percentage_funded}}%
<br>

<img src="data:image/png;base64,{!! base64_encode($project->qrcode) !!}">
