module.exports = {

    // Automatically clear mock calls and instances between every test
    clearMocks: true,

    roots:[
        "<rootDir>/resources/js"
    ],

    moduleFileExtensions: [
        "ts",
        "tsx",
        "js",
        "vue"
    ],

    // A map from regular expressions to module names that allow to stub out resources with a single module

    testEnvironment: "node",

    testRegex: "(/__tests__/.*|(\\.|/)(test|spec))\\.(jsx?|ts?)$",

    transform: {
        "^.+\\.(ts|tsx)$": "ts-jest",
        ".*\\.(vue)$": "vue-jest",
    },

    preset: 'ts-jest',
    testMatch: null,

    verbose: true,
    testURL: "http://127.0.0.1/"

};
