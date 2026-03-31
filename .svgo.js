export default {
    plugins: [
        {
            name: "preset-default",
            params: {
                overrides: {
                    cleanupIds: {
                        preservePrefixes: ["keep_"],
                    },
                },
            },
        },
    ],
};
