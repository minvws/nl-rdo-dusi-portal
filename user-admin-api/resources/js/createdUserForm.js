export const initCreatedUserForm = () => {
    const printButton = document.getElementById("executePrint");
    if (printButton) {
        printButton.addEventListener("click", function () {
            window.print();
        });
    }
};
