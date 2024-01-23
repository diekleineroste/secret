import { FormValidator } from "./FormValidator"
import { APIResponse } from "./util"

interface LoginData {
  accessToken: string
  userData: string
}

const accessToken = sessionStorage.getItem("accessToken")
if (accessToken != null) {
  window.location.href = "/my-profile/"
}

const loginForm: HTMLFormElement = document.querySelector("#login-form")
const username: HTMLInputElement = document.querySelector("#login-username")
const password: HTMLInputElement = document.querySelector("#login-password")
if (loginForm === undefined) {
  console.error("login-form undefined")
}

loginForm.addEventListener("submit", function (event) {
  event.preventDefault()
  if (formValidator.validate()) {
    formValidator.validate()
    formValidator.showInlineErrors()
    login(username.value, password.value)
      .then((response: APIResponse) => {
        const json: LoginData = JSON.parse(response.data) as LoginData
        sessionStorage.setItem("accessToken", json.accessToken)
        sessionStorage.setItem("userData", JSON.stringify(json.userData))
        console.log(JSON.stringify(json.userData))
        window.location.href = "/my-profile/"
      })
      .catch((error) => console.error(error))
  }
  this.reset()
})

const formValidator: FormValidator = new FormValidator(loginForm)

formValidator.addValidator({
  name: "username",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Username is required",
  field: username,
})

formValidator.addValidator({
  name: "password",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Password is required",
  field: password,
})

const login = async (name: string, password: string): Promise<APIResponse> => {
  const data: object = {
    username: name,
    password: password,
  }

  try {
    const response: Response = await fetch(
      import.meta.env.VITE_API_BASE_URL + "/api/auth/login",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      },
    )

    if (response.ok) {
      const json: APIResponse = (await response.json()) as APIResponse
      return json
    } else {
      console.error("Error login:", response.status)
      throw new Error("Error sending PUT request")
    }
  } catch (error) {
    console.error("Error sending PUT request:", error)
    throw error
  }
}
