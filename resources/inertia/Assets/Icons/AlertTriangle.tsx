export default function AlertTriangle({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            className={className}
        >
            <g
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
            >
                <path
                    strokeLinejoin="round"
                    d="M9.97 15.35c-.2 0-.3.1-.3.3s.1.3.3.3.3-.1.3-.3h0c0-.1-.2-.3-.3-.3h0m0-2.3v-6"
                />

                <path
                    strokeMiterlimit="10"
                    d="M11.17 1.85c-.4-.7-1.2-1-1.9-.7-.3.1-.5.4-.6.7l-7.6 15.4c-.3.6 0 1.3.6 1.6.2.1.3.1.5.1h15.6c.7 0 1.2-.5 1.2-1.2 0-.2 0-.4-.1-.5l-7.7-15.4z"
                />
            </g>
        </svg>
    );
}
