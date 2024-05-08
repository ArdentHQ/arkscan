export default {
    plugins: [
        {
            name: "preset-default",
            params: {
                overrides: {
                    cleanupIDs: {
                        preservePrefixes: ["keep_"],
                    },
                },
            },
        },
    ],
};
