export default function QRCode({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            className={className}
        >
            <path
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M8 1H1v7h7V1zm11 0h-7v7h7V1zM8 12H1v7h7v-7zm4 0h2m-2 3v4h2m1-3.5h4V12h-2m0 7h2"
                fill="none"
            />
        </svg>
    );
}
