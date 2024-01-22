import { FormValidator } from "./FormValidator"
import { APIResponse } from "./util"
interface LoginData {
  accessToken: string
  userData: string
}
const username: HTMLInputElement = document.querySelector("#username")
const password: HTMLInputElement = document.querySelector("#password")
const email: HTMLInputElement = document.querySelector("#email")
const phonenumber: HTMLInputElement = document.querySelector("#phonenumber")
const bio: HTMLInputElement = document.querySelector("#bio")
const dob: HTMLInputElement = document.querySelector("#date-of-birth")
const profilePicture: HTMLInputElement =
  document.querySelector("#profilePicture")
const street_no: HTMLInputElement = document.querySelector("#street_no")
const city: HTMLInputElement = document.querySelector("#city")
const zip: HTMLInputElement = document.querySelector("#zip")
const country: HTMLInputElement = document.querySelector("#country")

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
const phoneRegex = /^\+?\d+$/

const registerForm: HTMLFormElement = document.querySelector("#register-form")
if (registerForm === undefined) {
  console.error("registerForm undefined")
}

registerForm.addEventListener("submit", (event) => {
  event.preventDefault()
  formValidator.validate()
  formValidator.showInlineErrors()
  const formData = new FormData(registerForm)
  registerUser(formData)
    .then((response: APIResponse) => {
      alert("Account created!")
      const json: LoginData = JSON.parse(response.data) as LoginData
      sessionStorage.setItem("accessToken", json.accessToken)
      sessionStorage.setItem("userData", JSON.stringify(json.userData))
      console.log(JSON.stringify(json.userData))
      window.location.href = "/my-profile/"
    })
    .catch((error) => {
      console.error(error)
    })
})

const formValidator: FormValidator = new FormValidator(registerForm)

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

formValidator.addValidator({
  name: "email",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Password is required",
  field: email,
})

formValidator.addValidator({
  name: "email",
  method: (field: HTMLInputElement) => emailRegex.test(field.value.trim()),
  message: "Invalid email address",
  field: email,
})

formValidator.addValidator({
  name: "phonenumber",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Phonenumber is required",
  field: phonenumber,
})

formValidator.addValidator({
  name: "phonenumber",
  method: (field: HTMLInputElement) => phoneRegex.test(field.value.trim()),
  message: "Invalid phonenumber address",
  field: phonenumber,
})

formValidator.addValidator({
  name: "zip",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Zip is required",
  field: zip,
})

formValidator.addValidator({
  name: "country",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Country is required",
  field: country,
})

formValidator.addValidator({
  name: "street_no",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Street and number is required",
  field: street_no,
})

formValidator.addValidator({
  name: "city",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "City is required",
  field: city,
})

formValidator.addValidator({
  name: "profilePicture",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Profile picture is required",
  field: profilePicture,
})

formValidator.addValidator({
  name: "dob",
  method: (field: HTMLInputElement) => !isNaN(Date.parse(field.value)),
  message: "Invalid date format",
  field: dob,
})

formValidator.addValidator({
  name: "ageGreaterThan13",
  method: (field: HTMLInputElement) => {
    const dob = new Date(field.value)
    const today = new Date()
    const age = today.getFullYear() - dob.getFullYear()
    if (
      today.getMonth() < dob.getMonth() ||
      (today.getMonth() === dob.getMonth() && today.getDate() < dob.getDate())
    ) {
      return age - 1 >= 13
    } else {
      return age >= 13
    }
  },
  message: "User must be older than 13",
  field: dob,
})

formValidator.addValidator({
  name: "bio",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Bio is required",
  field: bio,
})

const registerUser = async (formData: FormData): Promise<APIResponse> => {
  try {
    const response = await fetch(
      import.meta.env.VITE_API_BASE_URL + "/api/auth/register",
      {
        method: "POST",
        body: formData,
      },
    )
    if (response.ok) {
      const json: APIResponse = (await response.json()) as APIResponse
      return json
    } else {
      console.error("Error sending PUT request:", response.status)
      throw new Error("Error sending PUT request")
    }
  } catch (error) {
    console.error(error)
  }
}
