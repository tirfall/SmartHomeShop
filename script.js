document.addEventListener("DOMContentLoaded", () => {
    // Добавляем обработчики событий для покупок
    const buyButtons = document.querySelectorAll(".buy-button");

    buyButtons.forEach(button => {
        button.addEventListener("click", async (event) => {
            event.preventDefault();

            const productId = button.getAttribute("data-product-id");

            try {
                const response = await fetch("purchase.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `product_id=${productId}`
                });

                const result = await response.text();
                alert(result);

                // Обновляем баланс пользователя после покупки (если нужно)
                if (result.includes("Покупка успешна")) {
                    const balanceElement = document.getElementById("user-balance");
                    const newBalance = await fetch("get_balance.php");
                    balanceElement.textContent = `Ваш баланс: ${await newBalance.text()} монет`;
                }
            } catch (error) {
                console.error("Ошибка:", error);
                alert("Произошла ошибка при выполнении покупки.");
            }
        });
    });
});
