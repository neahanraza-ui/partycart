// Product Data
const productsData = [
  {
    id: 1,
    name: "Silk Evening Gown",
    category: "Dresses",
    price: 1299,
    image:
      "https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&q=80",
      "https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&q=80",
      "https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&q=80",
    ],
    description:
      "Exquisite handcrafted silk evening gown with intricate gold embroidery. Perfect for exclusive events and elegant occasions. Made from the finest Italian silk with attention to every detail.",
    rating: 5,
    reviews: 89,
  },
  {
    id: 2,
    name: "Diamond Pendant",
    category: "Jewelry",
    price: 2499,
    image:
      "https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=800&q=80",
      "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&q=80",
    ],
    description:
      "Stunning 18k gold pendant featuring a flawless 2-carat diamond. Ethically sourced and expertly crafted by master jewelers.",
    rating: 5,
    reviews: 124,
  },
  {
    id: 3,
    name: "Leather Handbag",
    category: "Accessories",
    price: 899,
    image:
      "https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&q=80",
      "https://images.unsplash.com/photo-1564422170194-896b89110ef8?w=800&q=80",
    ],
    description:
      "Premium Italian leather handbag with gold-plated hardware. Timeless design meets modern functionality.",
    rating: 5,
    reviews: 156,
  },
  {
    id: 4,
    name: "Cashmere Blazer",
    category: "Outerwear",
    price: 1599,
    image:
      "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=80",
      "https://images.unsplash.com/photo-1507680434567-5739c80be1ac?w=800&q=80",
    ],
    description:
      "Luxurious cashmere blazer tailored to perfection. Features gold buttons and silk lining for ultimate comfort.",
    rating: 5,
    reviews: 98,
  },
  {
    id: 5,
    name: "Gold Watch",
    category: "Watches",
    price: 3299,
    image:
      "https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=800&q=80",
      "https://images.unsplash.com/photo-1524805444758-089113d48a6d?w=800&q=80",
    ],
    description:
      "Swiss-made automatic watch with 18k gold case. Limited edition timepiece with sapphire crystal and leather strap.",
    rating: 5,
    reviews: 203,
  },
  {
    id: 6,
    name: "Velvet Lounge Chair",
    category: "Furniture",
    price: 2199,
    image:
      "https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=800&q=80",
      "https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80",
    ],
    description:
      "Opulent velvet lounge chair with gold-finished frame. Perfect statement piece for luxury interiors.",
    rating: 5,
    reviews: 67,
  },
  {
    id: 7,
    name: "Designer Sunglasses",
    category: "Accessories",
    price: 599,
    image:
      "https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=800&q=80",
      "https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=800&q=80",
    ],
    description:
      "Handcrafted designer sunglasses with gold-plated frames and polarized lenses. UV400 protection.",
    rating: 5,
    reviews: 142,
  },
  {
    id: 8,
    name: "Pearl Necklace",
    category: "Jewelry",
    price: 1899,
    image:
      "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&q=80",
      "https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=800&q=80",
    ],
    description:
      "Lustrous South Sea pearl necklace with 18k gold clasp. Each pearl is hand-selected for perfection.",
    rating: 5,
    reviews: 178,
  },
  {
    id: 9,
    name: "Silk Scarf",
    category: "Accessories",
    price: 299,
    image:
      "https://images.unsplash.com/photo-1601924994987-69e26d50dc26?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1601924994987-69e26d50dc26?w=800&q=80",
    ],
    description:
      "Hand-painted pure silk scarf with gold accents. Lightweight and versatile luxury accessory.",
    rating: 5,
    reviews: 95,
  },
  {
    id: 10,
    name: "Marble Coffee Table",
    category: "Furniture",
    price: 1799,
    image:
      "https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?w=800&q=80",
    ],
    description:
      "Elegant marble coffee table with brushed gold base. Showcases natural stone patterns.",
    rating: 5,
    reviews: 83,
  },
  {
    id: 11,
    name: "Crystal Chandelier",
    category: "Lighting",
    price: 4299,
    image:
      "https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=800&q=80",
    ],
    description:
      "Magnificent hand-cut crystal chandelier with gold-plated frame. Creates stunning light reflections.",
    rating: 5,
    reviews: 45,
  },
  {
    id: 12,
    name: "Wool Coat",
    category: "Outerwear",
    price: 1299,
    image:
      "https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=800&q=80",
    images: [
      "https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=800&q=80",
    ],
    description:
      "Premium wool coat with tailored fit and gold button details. Timeless winter essential.",
    rating: 5,
    reviews: 167,
  },
];

// Collections Data
const collectionsData = [
  {
    id: 1,
    name: "Evening Elegance",
    description: "Sophisticated pieces for unforgettable nights",
    image:
      "https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&q=80",
    products: [1, 2, 7],
  },
  {
    id: 2,
    name: "Golden Heritage",
    description: "Timeless jewelry crafted with precision",
    image:
      "https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=800&q=80",
    products: [2, 5, 8],
  },
  {
    id: 3,
    name: "Modern Luxury",
    description: "Contemporary designs for the refined taste",
    image:
      "https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=800&q=80",
    products: [6, 10, 11],
  },
];
