export default function Cross({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            className={className}
        >
            <path
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M.954 18.981l18-18M18.954 18.981l-18-18"
            />
        </svg>
    );
}
