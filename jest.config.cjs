/** @type {import('jest').Config} */
module.exports = {
    preset: 'ts-jest',
    testEnvironment: 'node',
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/resources/inertia/$1',
        '^@inertiajs/react$': '<rootDir>/node_modules/@inertiajs/react/dist/index.js',
        '^@inertiajs/core$': '<rootDir>/node_modules/@inertiajs/core/dist/index.js',
    },
    transform: {
        '^.+\\.[jt]sx?$': ['ts-jest', {
            tsconfig: './tsconfig.test.json',
        }],
    },
    transformIgnorePatterns: [
        'node_modules/(?!(@inertiajs|es-toolkit)/)',
    ],
    testMatch: ['<rootDir>/resources/inertia/**/__tests__/**/*.test.ts'],
    testPathIgnorePatterns: ['/node_modules/', '/vendor/'],
};
