export default function SquareReturnArrow({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 17 16"
            className={className}
        >
            <path
                d="M5 8.602h4a2.4 2.4 0 0 0 2.4-2.4V5"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
            />

            <path
                d="m7 6.6-2 2 2 2"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
            />

            <path
                clip-rule="evenodd"
                d="M.805 2.858C.805 1.722 1.725.8 2.862.8h10.286c1.136 0 2.057.92 2.057 2.057v10.286a2.057 2.057 0 0 1-2.057 2.057H2.862a2.057 2.057 0 0 1-2.057-2.057V2.858Z"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
            />
        </svg>
    );
}
