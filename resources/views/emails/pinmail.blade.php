@component('mail::message')
# Introduction

account activation code is:  {{ $pin }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
