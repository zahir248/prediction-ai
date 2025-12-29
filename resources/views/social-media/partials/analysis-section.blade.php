<div style="margin-bottom: 32px; padding: 24px; background: white; border-radius: 12px; border: 1px solid #e2e8f0;">
    <h3 style="font-size: 22px; font-weight: 700; color: #0f172a; margin-bottom: 16px; letter-spacing: -0.02em; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">{{ $title }}</h3>
    
    @foreach($data as $key => $value)
        @if($key === 'recommendations' || $key === 'evidence' || $key === 'concerns' || $key === 'strengths' || $key === 'indicators' || $key === 'notable_patterns' || $key === 'key_characteristics')
            @if(is_array($value) && count($value) > 0)
                <div style="margin-bottom: 12px;">
                    <strong style="color: #374151;">{{ ucwords(str_replace('_', ' ', $key)) }}:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        @foreach($value as $item)
                            <li style="margin-bottom: 4px; color: #64748b; line-height: 1.6;">
                                @if(is_string($item))
                                    {{ $item }}
                                @elseif(is_array($item))
                                    {{ json_encode($item, JSON_PRETTY_PRINT) }}
                                @else
                                    {{ $item ?? '' }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @elseif(is_string($value) && trim($value) !== '')
            <div style="margin-bottom: 12px;">
                <strong style="color: #374151;">{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> 
                <span style="color: #64748b; line-height: 1.6;">{{ $value }}</span>
            </div>
        @endif
    @endforeach
</div>

