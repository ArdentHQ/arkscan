export default function ChevronDownSmall({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 10 10"
            className={className}
        >
            <path
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M9 3.1L5.2 6.9c-.1.1-.3.1-.4 0L1 3.1"
            />
        </svg>
    );
}
