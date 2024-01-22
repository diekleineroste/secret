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

interface Comment {
  id: number
  content: string
  likes: number
  artpiece_id: number
  created_at: Date
  user_id: number
}

interface APIResponse {
  success: boolean
  message: string
  data: string //JSON String
}

const urlParams = new URLSearchParams(window.location.search)
const workContainer: HTMLElement = document.querySelector("#work-container")

const fetchUserDetails = async (userId) => {
  const userResponse = await fetch(
    `${import.meta.env.VITE_API_BASE_URL}/api/artists/${userId}`,
  )
  if (!userResponse.ok) {
    throw new Error(`Failed to fetch user details: ${userResponse.statusText}`)
  }
  const userResponseJson: APIResponse =
    (await userResponse.json()) as APIResponse
  return JSON.parse(userResponseJson.data) as Artist
}

const getArtpiece = async () => {
  try {
    let queryParams = ""
    if (urlParams.get("id") !== null) queryParams += `${urlParams.get("id")}`
    const response = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/api/artpieces/${queryParams}`,
    )
    if (!response.ok) {
      throw new Error(`Failed to fetch artpiece: ${response.statusText}`)
    }

    const responseJson: APIResponse = (await response.json()) as APIResponse
    const artpiece: string = responseJson.data
    const work: Work = JSON.parse(artpiece) as Work
    const id: number = work.user_id

    const artistResponse = await fetch(
      `${import.meta.env.VITE_API_BASE_URL}/api/artists/${id}`,
    )
    if (!artistResponse.ok) {
      throw new Error(
        `Failed to fetch artpiece artist: ${artistResponse.statusText}`,
      )
    }
    const artistResponseJson: APIResponse =
      (await artistResponse.json()) as APIResponse
    const artistJsonString: string = artistResponseJson.data
    const artpieceArtist: Artist = JSON.parse(artistJsonString) as Artist

    const commentsResponse = await fetch(
      `${
        import.meta.env.VITE_API_BASE_URL
      }/api/artpieces/${queryParams}comments`,
    )
    let comments: Comment[]
    if (commentsResponse.ok) {
      const commentsResponseJson: APIResponse =
        (await commentsResponse.json()) as APIResponse
      const commentsJsonString = commentsResponseJson.data
      comments = JSON.parse(commentsJsonString) as Comment[]
    }
    console.log(comments)
    const artDetail: HTMLElement = document.createElement("section")
    artDetail.classList.add("artwork")
    artDetail.innerHTML = `
    <div class="work-detail">
      <img
        class="work-img"
        src="${import.meta.env.VITE_API_BASE_URL}/images/${work.id}.jpg"
        alt="photo of ${work.name}"
      />
      <div class="center">
        <div>
          <h1>${work.name}</h1>
          <span>€ ${work.price}</span>
        </div>
        <div class="work-tags">
          <h2>Tags</h2>
          <ul>
            <li>${work.category}</li>
          </ul>
        </div>
        <div class="work-dimensions">
          <h2>Dimensions</h2>
          <p>${work.height_in_cm}cm x ${work.width_in_cm}cm</p>
        </div>
        <div class="work-description">
          <h2>Description</h2>
          <p>${work.description}</p>
        </div>
      </div>
      <div class="right">
        <div class="artist">
          <img src="${import.meta.env.VITE_API_BASE_URL}/profilePictures/${
            artpieceArtist.profile_picture
          }" alt="profile picture of ${artpieceArtist.username}" />
          <div>
            <span class="popular-artist-name">${artpieceArtist.username}</span>
          </div>
        </div>
        <a class="buy-button" href="">
          <button>Buy</button>
        </a>
      </div>
    </div>
    <div class="comments">
      <form action="">
        <label for="add-comment">Add comment</label>
        <input
          class="add-comment"
          type="text"
          name="add-comment"
          id="add-comment"
          placeholder="Add a comment..."
        />
        <input class="submit-comment" type="submit" value="Add" />
      </form>
      <ul class="comment-list">
        ${(
          await Promise.all(
            comments.map(async (comment: Comment) => {
              const user = await fetchUserDetails(comment.user_id)

              return `
              <li>
                <img src="${
                  import.meta.env.VITE_API_BASE_URL
                }/profilePictures/${
                  user.profile_picture
                }" alt="profile picture of ${user.username}" />
                <div>
                  <h3>${user.username}</h3>
                  <p>${comment.content}</p>
                </div>
              </li>
            `
            }),
          )
        ).join("")}
      </ul>
    </div>
    `
    workContainer.appendChild(artDetail)
    console.log(artpieceArtist, artpiece)
  } catch (error) {
    console.error("Error fetching artist:", error)
  }
}

getArtpiece().catch((error) => {
  console.error(error)
})
