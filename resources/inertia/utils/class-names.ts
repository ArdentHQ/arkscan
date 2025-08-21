export default function classNames(classes: Record<string, boolean>) {
    return Object.entries(classes)
        .filter(([_, value]) => value)
        .map(([key, _]) => key)
        .join(' ');
}
