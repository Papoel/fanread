import { defineConfig } from '@maizzle/framework'

export default defineConfig({
  content: ['emails/**/*.{vue,md}', 'emails/**/*.html'],
  static: {
      source: ['images/**/*.*'],
      destination: 'images',
    },
  output: {
    path: '../templates/emails',
  }
})
