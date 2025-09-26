export default function classNames(classes: Record<string, boolean | CallableFunction>, data: Record<string, any> = {}): string {
    return Object.entries(classes)
        .filter(([_, value]) => value)
        .map(([key, value]) => {
            if (typeof value === 'function') {
                return value(data);
            }

            return key;
        })
        .join(' ');
}
