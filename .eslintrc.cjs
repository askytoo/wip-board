// eslint-disable-next-line @typescript-eslint/no-var-requires
const path = require("path");

/** @type {import("eslint").Linter.Config} */
const config = {
    overrides: [
        {
            extends: [
                "plugin:@typescript-eslint/recommended-requiring-type-checking",
            ],
            files: ["*.svelte"],
            processor: "svelte3/svelte3",
            parserOptions: {
                project: path.join(__dirname, "tsconfig.json"),
            },
        },
    ],
    parser: "@typescript-eslint/parser",
    parserOptions: {
        project: path.join(__dirname, "tsconfig.json"),
    },
    plugins: ["@typescript-eslint", "svelte3"],
    extends: ["plugin:@typescript-eslint/recommended"],
    rules: {
        "@typescript-eslint/consistent-type-imports": [
            "warn",
            {
                prefer: "type-imports",
                fixStyle: "inline-type-imports",
            },
        ],
        "@typescript-eslint/no-unused-vars": [
            "warn",
            { argsIgnorePattern: "^_" },
        ],
        indent: ["off", 2],
    },
};

module.exports = config;
