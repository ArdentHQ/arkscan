/** @type {import('jest').Config} */
module.exports = {
    preset: 'ts-jest',
    testEnvironment: 'node',
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/resources/inertia/$1',
    },
    transform: {
        '^.+\\.tsx?$': ['ts-jest', {
            tsconfig: './tsconfig.test.json',
        }],
    },
    testMatch: ['<rootDir>/resources/inertia/**/__tests__/**/*.test.ts'],
    testPathIgnorePatterns: ['/node_modules/', '/vendor/'],
};
