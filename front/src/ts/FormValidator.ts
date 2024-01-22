interface Validator {
  name: string
  method: (field: HTMLInputElement) => boolean
  message: string
  field: HTMLInputElement | Array<HTMLInputElement>
}

export class FormValidator {
  form: HTMLFormElement
  validators: Validator[] = []
  errors: Validator[] = []

  constructor(form: HTMLFormElement) {
    this.form = form
    this.form.addEventListener("submit", (event) => this.onSubmit(event))
  }

  addValidator(validator: Validator): void {
    this.validators.push({
      ...validator,
      field: this.form.elements[validator.name] as HTMLInputElement,
    })
  }

  validate(): boolean {
    this.validators.forEach((validator: Validator) => {
      const index: number = this.errors.findIndex(
        (x) => x.name === validator.name,
      )
      if (index > -1) {
        return
      }
      if (validator.field instanceof HTMLInputElement) {
        if (!validator.method(validator.field)) {
          this.errors.push(validator)
        }
      }
    })

    return this.errors.length === 0
  }

  onSubmit(event: Event): void {
    this.resetSummary()
    this.removeInlineErrors()

    if (!this.validate()) {
      event.stopImmediatePropagation()
      event.preventDefault()
      this.validate()
      this.showInlineErrors()
      this.showSummary()
    }
  }

  createInlineError(error: Validator): HTMLElement {
    const span: HTMLElement = document.createElement("span")

    span.className = "field-error"
    span.innerText = error.message
    span.id = `${error.name}-error`

    return span
  }

  showInlineErrors(): void {
    this.errors.forEach((error) => {
      const errorElement = this.createInlineError(error)

      if (error.field instanceof HTMLInputElement) {
        error.field.classList.add("invalid")
        error.field.setAttribute("aria-invalid", "true")
        error.field.labels[0]?.appendChild(errorElement)
      } else if (error.field instanceof NodeList) {
        error.field.forEach((node) => {
          if (node instanceof HTMLElement) {
            node.classList.add("invalid")
            node.setAttribute("aria-describedby", errorElement.id)
            node.setAttribute("aria-invalid", "true")
          }
        })

        const fieldSet = error.field[0]?.closest("fieldset")
        const legend = fieldSet?.querySelector("legend")
        if (legend instanceof HTMLElement) {
          legend.appendChild(errorElement)
        }
      }
    })
  }

  removeInlineErrors(): void {
    this.form
      .querySelectorAll(".field-error")
      .forEach((element: HTMLElement) => element.remove())
    this.form.querySelectorAll(".invalid").forEach((element) => {
      element.removeAttribute("aria-invalid")
      element.removeAttribute("aria-describedby")
      element.classList.remove("invalid")
    })
    this.errors = []
  }
  showSummary(): void {}
  resetSummary(): void {}
}
