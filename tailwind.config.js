import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./public/assets/js/*.js",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                purple: {
                    50: "#f4f1fb",
                    100: "#e9e3f7",
                    200: "#d2c8f0",
                    300: "#bcaee8",
                    400: "#a693e0",
                    500: "#8f79d8",
                    600: "#7e67be",
                    700: "#615092",
                    800: "#443866",
                    900: "#27203b",
                },
            },
        },
    },

    plugins: [forms],
};
