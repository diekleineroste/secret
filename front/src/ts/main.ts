interface Artist {
  id: number
  username: string
  email: string
  profile_picture: string
  bio: string
  total_likes: number
}

interface Work {
  id: number
  name: string
  description: string
  width_in_cm: number
  height_in_cm: number
  price: number
  type: string
  likes: number
  user_id: number
  created_at: string
  category: string
  auction_id: number | null
  username: string
  email: string
  profile_picture: string
  bio: string
}

interface APIResponse {
  success: boolean
  message: string
  data: string //JSON String
}

const popularArtistsContainer: HTMLElement = document.querySelector(
  "#popular-artists-container",
)
const popularWorksContainer: HTMLElement = document.querySelector(
  "#popular-works-container",
)
const form: HTMLFormElement = document.querySelector("#artForm")
if (form === undefined) {
  console.error("undefined")
}

//const formValidator: FormValidator = new FormValidator(form)
const category: HTMLInputElement = document.querySelector("#category")
const buytype: HTMLInputElement = document.querySelector("#buytype")
const search: HTMLInputElement = document.querySelector("#search")
const inputMin: HTMLInputElement = document.querySelector("#min")
const inputMax: HTMLInputElement = document.querySelector("#max")

const min: number = +inputMin.value
const max: number = +inputMax.value
/*const categories: string[] = [
  "paintings",
  "photographs",
  "sculptures",
  "drawings",
]*/
console.log(min, max)
form.addEventListener("submit", function (event) {
  event.preventDefault()
  let queryParams: string
  if (search.value) queryParams += `&name=${encodeURIComponent(search.value)}`
  if (buytype.checked) queryParams += `&isAuction=${buytype.checked}`
  if (category.value) queryParams += `&filter=${category.value}`
  if (parseInt(inputMin.value))
    queryParams += `&minPrice=${parseInt(inputMin.value)}`
  if (parseInt(inputMax.value))
    queryParams += `&maxPrice=${parseInt(inputMax.value)}`
  window.location.href = `/products/index.html?${queryParams}`
  this.reset()
  checkIsAuction()
})

const isAuction: HTMLInputElement = document.querySelector("#buytype")
const buytypeLabel: HTMLElement = document.querySelector("#buytypelabel")
isAuction.addEventListener("change", () => {
  checkIsAuction()
})

const checkIsAuction = (): void => {
  if (isAuction.checked) {
    buytypeLabel.classList.add("clicked")
  } else {
    buytypeLabel.classList.remove("clicked")
  }
}

const getPopularArtists = async () => {
  try {
    const url: string = `${
      import.meta.env.VITE_API_BASE_URL
    }/api/artists/popular/`

    const response = await fetch(url)
    if (!response.ok) {
      throw new Error(`Failed to fetch artists: ${response.statusText}`)
    }
    const responseJson: APIResponse = (await response.json()) as APIResponse
    const artists: string = responseJson.data
    console.log(artists)
    return artists
  } catch (error) {
    console.error("Error fetching artists:", error)
    return []
  }
}

const getPopularWorks = async () => {
  try {
    const url: string = `${
      import.meta.env.VITE_API_BASE_URL
    }/api/artpieces?page=0&sort=likes`

    const response = await fetch(url)
    if (!response.ok) {
      throw new Error(`Failed to fetch art pieces: ${response.statusText}`)
    }
    const responseJson: APIResponse = (await response.json()) as APIResponse
    const artpieces: string = responseJson.data
    console.log(artpieces)
    return artpieces
  } catch (error) {
    console.error("Error fetching artists:", error)
    return []
  }
}

const createPopularArtists = (artists: Artist[]): void => {
  if (Array.isArray(artists) && artists.length != 0) {
    for (let i = 0; i < 3; i++) {
      const artist: Artist = artists[i]
      const popularArtist: HTMLElement = document.createElement("li")
      popularArtist.innerHTML = `
      <a class="popular-artist-card" href="/profile-detail/?id=${artist.id}">
        <img src="${import.meta.env.VITE_API_BASE_URL}/profilePictures/${
          artist.profile_picture
        }" alt="${artist.username}" />
        <span class="popular-artist-name">${artist.username}</span>
        <hr />
        <span class="popular-artist-works">${artist.total_likes} Likes</span>
      </a>
      `
      popularArtistsContainer.appendChild(popularArtist)
    }
  } else {
    console.error("Error creating popular artists", artists)
  }
}

const createPopularWorks = (works: Work[]): void => {
  if (Array.isArray(works) && works.length != 0) {
    for (let i = 0; i < 3; i++) {
      const work: Work = works[i]
      const popularWork: HTMLElement = document.createElement("li")
      popularWork.innerHTML = `
        <a class="popular-work-card" href="art-detail/?id=${work.id}/">
          <img src="${import.meta.env.VITE_API_BASE_URL}/images/${
            work.id
          }.jpg" alt="image of ${work.name}" />
          <div class="popular-work-details">
            <span class="popular-work-specification">${work.category}</span>
            <span class="popular-work-name">${work.name}</span>
            <span class="popular-work-price">€ ${work.price}</span>
          </div>
          <hr />
          <div class="popular-work-profile">
            <img src="${import.meta.env.VITE_API_BASE_URL}/profilePictures/${
              work.profile_picture
            }" alt="${work.username} profile picture" />
            <span class="popular-work-artist">${work.username}</span>
          </div>
        </a>
      `
      popularWorksContainer.appendChild(popularWork)
    }
  } else {
    console.error("Error creating popular works", works)
  }
}

getPopularArtists()
  .then((artists: string) => {
    createPopularArtists(JSON.parse(artists) as Artist[])
  })
  .catch((error) => {
    console.error(error)
  })

getPopularWorks()
  .then((works: string) => {
    createPopularWorks(JSON.parse(works) as Work[])
  })
  .catch((error) => {
    console.error(error)
  })
