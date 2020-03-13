@component('mail::message')
# Prooflo

Please go to the proof and make your decision.

@component('mail::button', ['url' => $url])
Open Proof
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent