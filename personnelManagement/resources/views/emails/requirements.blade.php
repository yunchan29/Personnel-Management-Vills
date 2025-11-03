@extends('emails.layout')
@section('title', 'Missing Requirements Notification')

@section('content')
<h2 style="margin:0 0 12px; font-size:22px; color:#0f172a;">
  Dear {{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }},
</h2>

<p style="margin:0 0 16px; line-height:1.6;">
  Upon checking your employment requirements, we have noticed that some of the required documents are still missing. Please review the list below and submit them at your earliest convenience.
</p>

@if(!empty($missingRequirements))
  <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" 
         style="margin:16px 0; background:#fff7ed; border:1px solid #fed7aa; border-radius:6px;">
    <tr>
      <td style="padding:14px 16px;">
        <ul style="margin:0; padding-left:20px; font-size:14px; color:#334155;">
          @foreach($missingRequirements as $item)
            <li>{{ $item }}</li>
          @endforeach
        </ul>
      </td>
    </tr>
  </table>
@endif

<p style="margin:0 0 12px; line-height:1.6;">
  You may submit the missing documents directly through the portal. Ensuring your records are complete will help us process your employment without delay.
</p>

<p style="margin:0;">Thank you for your cooperation and prompt attention to this matter.</p>
@endsection
