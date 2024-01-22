interface Artist {
  id: number
  username: string
  email: string
  profile_picture: string
  bio: string
  total_likes: number
  phonenumber: string
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

// Retrieve accessToken from sessionStorage
const accessToken = sessionStorage.getItem("accessToken")

// Retrieve userData from sessionStorage and parse it as JSON
const userData: Artist = JSON.parse(
  sessionStorage.getItem("userData"),
) as Artist

console.log(accessToken, userData)

if (accessToken === null || userData == null) {
  window.location.href = "/authentication/"
}

const artistContainer: HTMLElement = document.querySelector("#artist-container")
const worksContainer: HTMLElement = document.querySelector("#works-container")
const getUserArtpieces = async () => {
  try {
    const url: string = `${import.meta.env.VITE_API_BASE_URL}/api/artists/${
      userData.id
    }/artpieces`

    const response = await fetch(url)
    if (!response.ok) {
      throw new Error(`Failed to fetch artist: ${response.statusText}`)
    }
    const responseJson: APIResponse = (await response.json()) as APIResponse
    const works: string = responseJson.data
    return works
  } catch (error) {
    console.error("Error fetching artists:", error)
    return ""
  }
}

const createPopularArtists = (artist: Artist): void => {
  if (artist !== null) {
    const artistInfo: HTMLElement = document.createElement("div")
    artistInfo.classList.add("profile-detail")
    artistInfo.innerHTML = `
      <div class="left">
        <img
          class="profile-img"
          src="${import.meta.env.VITE_API_BASE_URL}/profilePictures/${
            artist.profile_picture
          }"
          alt="picture of ${artist.username}"
          id="profilePicture"
        />
        <h1 id="username">${artist.username}</h1>
      </div>
      <div class="right">
        <h2>Bio</h2>
        <hr />
        <p id="bio">
          ${artist.bio}
        </p>
      </div>
      <div class="personal-info">
        <p id="email">${artist.email}</p>
        <p id="phoneNumber">${artist.phonenumber}</p>
      </div>
    `
    artistContainer.appendChild(artistInfo)
    console.log("Created")
  } else {
    console.error("Error creating popular artists", artist)
  }
}

const createWorks = (works: Work[]): void => {
  if (Array.isArray(works) && works.length != 0) {
    works.forEach((work: Work) => {
      const workCard: HTMLElement = document.createElement("li")
      workCard.innerHTML = `
        <a class="work-card" href="../art-detail?id=${work.id}/">
          <img src="${import.meta.env.VITE_API_BASE_URL}/images/${
            work.id
          }.jpg" alt="image of ${work.name}" />
          <div class="work-details">
            <span class="work-specification">${work.category}</span>
            <span class="work-name">${work.name}</span>
            <span class="work-price">€ ${work.price}</span>
          </div>
        </a>
      `
      worksContainer.appendChild(workCard)
    })
  } else {
    console.error("Error creating popular works", works)
  }
}

createPopularArtists(userData)
getUserArtpieces()
  .then((works: string) => {
    createWorks(JSON.parse(works) as Work[])
  })
  .catch((error) => {
    console.error(error)
  })
