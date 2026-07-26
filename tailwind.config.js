import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            colors: {
                ink: "#0F172A",
                inkline: "#1E293B",
                paper: "#F1F5F9",
                accent: "#14B8A6",
                income: "#059669",
                expense: "#E11D48",
            },
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
                display: ["Sora", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
