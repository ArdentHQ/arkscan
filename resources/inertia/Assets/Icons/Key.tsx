export default function Key({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            className={className}
        >
            <g
                transform="translate(-35.75 84.006)"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
            >
                <circle
                    cx="40.8"
                    cy="-74"
                    r="4"
                />

                <path d="M44.8-74h10v3M50.8-74v2" />
            </g>
        </svg>
    );
}
