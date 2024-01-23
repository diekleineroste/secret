import { FormValidator } from "./FormValidator"
import { APIResponse } from "./util"
// interface LoginData {
//   accessToken: string
//   userData: string
// }
const accessToken = sessionStorage.getItem("accessToken")
if (accessToken === null) {
  window.location.href = "/authentication/"
}
const addForm: HTMLFormElement = document.querySelector("#add-form")
const name: HTMLInputElement = document.querySelector("#name")
const picture: HTMLInputElement = document.querySelector("#picture")
const description: HTMLInputElement = document.querySelector("#description")
const price: HTMLInputElement = document.querySelector("#price")
const width: HTMLInputElement = document.querySelector("#width")
const height: HTMLInputElement = document.querySelector("#height")
if (addForm === undefined) {
  console.error("add-form undefined")
}

addForm.addEventListener("submit", function (event) {
  event.preventDefault()
  if (formValidator.validate()) {
    formValidator.validate()
    formValidator.showInlineErrors()
    console.log("cool")
    const formData: FormData = new FormData(addForm)
    postArtpiece(formData)
      .then((response) => {
        console.log("Artpiece created", response)
      })
      .catch((error) => {
        console.error(error)
      })
  }
  this.reset()
})

const formValidator: FormValidator = new FormValidator(addForm)

// Title Validator
formValidator.addValidator({
  name: "name",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Title is required",
  field: name,
})

// Picture Validator
formValidator.addValidator({
  name: "profilePicture",
  method: (field: HTMLInputElement) => {
    const allowedExtensions = ["jpg"]
    const fileName = field.value.toLowerCase()
    return allowedExtensions.some((ext) => fileName.endsWith(`.${ext}`))
  },
  message: "Please upload a valid .jpg file",
  field: picture,
})

// Description Validator
formValidator.addValidator({
  name: "description",
  method: (field: HTMLInputElement) => field.value.trim().length > 0,
  message: "Description is required",
  field: description,
})

// Price Validator
formValidator.addValidator({
  name: "price",
  method: (field: HTMLInputElement) => parseFloat(field.value) > 0,
  message: "Price must be greater than 0",
  field: price,
})

formValidator.addValidator({
  name: "width_in_cm",
  method: (field: HTMLInputElement) => parseFloat(field.value) > 0,
  message: "Width must be greater than 0",
  field: width,
})

formValidator.addValidator({
  name: "height_in_cm",
  method: (field: HTMLInputElement) => parseFloat(field.value) > 0,
  message: "Height must be greater than 0",
  field: height,
})

const postArtpiece = async (formData: FormData): Promise<APIResponse> => {
  try {
    const response = await fetch(
      import.meta.env.VITE_API_BASE_URL + "/api/artpieces",
      {
        method: "POST",
        headers: {
          "X-Authorization": "Bearer " + accessToken,
        },
        body: formData,
      },
    )
    if (response.ok) {
      const json: APIResponse = (await response.json()) as APIResponse
      return json
    } else {
      console.error("Error sending PUT request:", response.status)
      alert("Could not input artpiece")
      throw new Error("Error sending PUT request")
    }
  } catch (error) {
    console.error(error)
  }
}
