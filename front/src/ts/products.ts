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

const worksContainer: HTMLElement = document.querySelector("#works-container")
const artistsContainer: HTMLElement =
  document.querySelector("#artists-container")
const urlParams = new URLSearchParams(window.location.search)

const getArtists = async () => {
  try {
    let queryParams = "page=0"
    if (urlParams.get("name") !== null)
      queryParams += `&search=${urlParams.get("name")}`
    const response = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/api/artists?${queryParams}`,
    )

    if (!response.ok) {
      throw new Error(`Failed to fetch art pieces: ${response.statusText}`)
    }

    const responseJson: APIResponse = (await response.json()) as APIResponse
    const artists: string = responseJson.data
    console.log(artists)
    return artists
  } catch (error) {
    console.error("Error fetching art pieces:", error)
    return []
  }
}

const getArtpieces = async () => {
  try {
    let queryParams = "page=0"
    if (urlParams.get("name") !== null)
      queryParams += `&search=${urlParams.get("name")}`
    if (urlParams.get("filter") !== null)
      queryParams += `&filter=${urlParams.get("filter")}`
    if (urlParams.get("maxPrice") !== null)
      queryParams += `&maxPrice=${urlParams.get("maxPrice")}`
    if (urlParams.get("minPrice") !== null)
      queryParams += `&minPrice=${urlParams.get("minPrice")}`
    const response = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/api/artpieces?${queryParams}`,
    )

    if (!response.ok) {
      throw new Error(`Failed to fetch art pieces: ${response.statusText}`)
    }

    const responseJson: APIResponse = (await response.json()) as APIResponse
    const artpieces: string = responseJson.data
    console.log(artpieces)
    return artpieces
  } catch (error) {
    console.error("Error fetching art pieces:", error)
    return []
  }
}

const createWorks = (works: Work[]): void => {
  if (Array.isArray(works) && works.length != 0) {
    works.forEach((work: Work) => {
      const workCard: HTMLElement = document.createElement("li")
      workCard.innerHTML = `
        <a class="work-card" href="../art-detail/?id=${work.id}">
          <img src="${import.meta.env.VITE_API_BASE_URL}/images/${
            work.id
          }.jpg" alt="image of ${work.name}" />
          <div class="work-details">
            <span class="work-specification">${work.category}</span>
            <span class="work-name">${work.name}</span>
            <span class="work-price">€ ${work.price}</span>
          </div>
          <hr />
          <div class="work-profile">
            <img src="${import.meta.env.VITE_API_BASE_URL}/profilePictures/${
              work.profile_picture
            }" alt="${work.username} profile picture" />
            <span class="work-artist">${work.username}</span>
          </div>
        </a>
      `
      worksContainer.appendChild(workCard)
    })
  } else {
    console.error("Error creating popular works", works)
  }
}

const createArtists = (artists: Artist[]): void => {
  if (Array.isArray(artists) && artists.length != 0) {
    for (let i = 0; i < 7; i++) {
      const artist: Artist = artists[i]
      const artistCard: HTMLElement = document.createElement("li")
      artistCard.innerHTML = `
      <a class="artist-card" href="../profile-detail/?id=${artist.id}">
        <img src="${import.meta.env.VITE_API_BASE_URL}/profilePictures/${
          artist.profile_picture
        }" alt="${artist.username}" />
        <span class="artist-name">${artist.username}</span>
        <hr />
        <span class="artist-works">${artist.total_likes} Likes</span>
      </a>
      `
      artistsContainer.appendChild(artistCard)
    }
  } else {
    console.error("Error creating popular artists", artists)
  }
}

getArtpieces()
  .then((works: string) => {
    createWorks(JSON.parse(works) as Work[])
  })
  .catch((error) => {
    console.error("Error:", error)
  })

getArtists()
  .then((artists: string) => {
    createArtists(JSON.parse(artists) as Artist[])
  })
  .catch((error) => {
    console.error("Error:", error)
  })
