/** @type {import('next').NextConfig} */
const nextConfig = {
  // Static export served under /wildflower (deployment-specific — leave as is
  // unless the hosting path changes).
  output: 'export',
  basePath: '/wildflower',
  trailingSlash: true,
  images: {
    unoptimized: true,
  },
}

export default nextConfig
