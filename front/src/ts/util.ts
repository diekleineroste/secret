export interface Artpiece {
  id: number
  name: string
  description: string
  images: string[]
  width_in_cm: number
  height_in_cm: number
  price: number
  type: "auction" | "buynow"
  comments: Array<Comment>
  likes: number
  category: "pictures" | "sculptures" | "paintings" | "drawings"
  created_at: Date
}

export interface Comment {
  id: number
  author: User
  content: string
  likes: number
  created_at: Date
}

export interface User {
  id: number
  username: string
  email: string
  bio: string
  date_of_birth: Date
  address: Address
  phonenumber: number
  profile_picture: string
  role: "admin" | "user"
  created_at: Date
}

export interface Artist {
  id: number
  username: string
  email: string
  profile_picture: string
  bio: string
  total_likes: number
}

export interface Address {
  street: string
  house_number: number
  city: string
  zip: number
  country: string
}

export interface Order {
  id: number
  user: User
  address: Address
  items: Array<Artpiece>
  status: string
  created_at: Date
}

export interface Work {
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

export interface APIResponse {
  success: boolean
  message: string
  data: string //JSON String
}

export interface LoginData {
  accessToken: string
  userData: string
}
