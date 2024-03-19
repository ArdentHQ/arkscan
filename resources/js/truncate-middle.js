export const truncateMiddle = (text, maxLength = 10) => {
    if (typeof text !== 'string') {
        return text;
    }

    const value = text.trim();
    if (value.length <= maxLength) {
        return value;
    }

    let partLength = Math.floor(maxLength / 2);

    let parts = [
        value.substring(0, partLength),
        value.substring(value.length - partLength)
    ];

    return parts.join('…');
};
