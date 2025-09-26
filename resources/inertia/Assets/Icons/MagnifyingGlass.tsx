export default function MagnifyingGlass({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            className={className}
        >
            <g
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12.1 16.4c4.1-1.7 6-6.5 4.3-10.5C14.7 1.8 9.9 0 5.9 1.6s-6 6.5-4.3 10.5h0c1.8 4.1 6.4 6 10.5 4.3h0zM14.7 14.7L19 19"
                />
            </g>
        </svg>
    );
}
