export default function Clock({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            className={className}
        >
            <g
                transform="translate(1.5 1.5)"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeMiterlimit="10"
            >
                <circle
                    cx="8.5"
                    cy="8.5"
                    r="9"
                />

                <path d="M8.5 8.5V5.2M8.5 8.5l3.9 3.9" />
            </g>
        </svg>
    );
}
