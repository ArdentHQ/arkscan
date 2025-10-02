export default function EllipsisVertical({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 16 17"
            className={className}
        >
            <circle
                cx="8.002"
                cy="2.897"
                r="1.6"
                transform="rotate(90 8.002 2.897)"
                fill="currentColor"
            />

            <circle
                cx="8.002"
                cy="8.498"
                r="1.6"
                transform="rotate(90 8.002 8.498)"
                fill="currentColor"
            />

            <circle
                cx="8.002"
                cy="14.1"
                r="1.6"
                transform="rotate(90 8.002 14.1)"
                fill="currentColor"
            />
        </svg>
    );
}
