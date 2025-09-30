export default function percentage(value: number, decimals: number = 2): string {
    // sprintf('%0.'.$decimals.'f', $value, $decimals).'%'
    return value.toFixed(decimals) + '%';
}
