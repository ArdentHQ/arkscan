export default function HintSmall({ className = "" }: { className?: string }) {
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
                d="M7 9H5.503A.503.503 0 015 8.497V4.503A.503.503 0 004.497 4H3.5"
            />

            <circle
                fill="currentColor"
                cx="4.5"
                cy="1"
                r="1"
            />
        </svg>
    );
}
