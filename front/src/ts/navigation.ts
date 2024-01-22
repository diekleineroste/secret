const menuButton: HTMLButtonElement = document.querySelector("#menu")
const navigationList: HTMLElement = document.querySelector("#navigation-list")

menuButton.addEventListener("click", () => {
  menuButton.setAttribute(
    "aria-expanded",
    menuButton.getAttribute("aria-expanded") === "true" ? "false" : "true",
  )
  if (menuButton.getAttribute("aria-expanded") === "true") {
    navigationList.classList.add("visible")
    navigationList.classList.remove("hidden")
  } else {
    navigationList.classList.remove("visible")
    navigationList.classList.add("hidden")
  }
})
