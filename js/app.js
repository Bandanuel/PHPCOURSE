"use strict";

const categories = [...new Set(products.map(product => product.category))];

const categoryFilter = document.querySelector("#categoryFilter");

categories.forEach(category => {
    const option = document.createElement("option");

    option.value = category;
    option.textContent = category;

    categoryFilter.append(option);
});

const cards = document.querySelectorAll(".product-card");

categoryFilter.addEventListener("change", () => {
    const selectedCategory = categoryFilter.value;

    cards.forEach(card => {
        const cardCategory = card.dataset.category;

        if (
            selectedCategory === "all" ||
            cardCategory === selectedCategory
        ) {
            card.style.display = "";
        } else {
            card.style.display = "none";
        }
    });
});

const searchInput = document.querySelector("#searchInput");

searchInput.addEventListener("input", () => {
    const searchText = searchInput.value.toLowerCase().trim();

    cards.forEach(card => {
        const productName =
            card.querySelector("h2").textContent.toLowerCase();

        card.style.display =
            productName.includes(searchText) ? "" : "none";
    });
});

function filterProducts() {
    const selectedCategory = categoryFilter.value;
    const searchText = searchInput.value.toLowerCase().trim();

    cards.forEach(card => {
        const cardCategory = card.dataset.category;
        const productName =
            card.querySelector("h2").textContent.toLowerCase();

        const categoryMatches =
            selectedCategory === "all" ||
            cardCategory === selectedCategory;

        const searchMatches =
            productName.includes(searchText);

        card.style.display =
            categoryMatches && searchMatches ? "" : "none";
    });
}

categoryFilter.addEventListener("change", filterProducts);
searchInput.addEventListener("input", filterProducts);

console.log(categories);
