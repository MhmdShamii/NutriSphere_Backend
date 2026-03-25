@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}"
   style="
    background: linear-gradient(135deg, #22c55e, #4ade80);
    color: #0f172a;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    box-shadow: 0 0 12px rgba(34,197,94,0.6);
   "
   target="_blank">
   {!! $slot !!}
</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
