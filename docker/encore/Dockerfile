FROM node:latest

ARG appDir=/app
ENV PATH "${appDir}/node_modules/.bin:${PATH}"

WORKDIR ${appDir}

EXPOSE 8080

CMD [ "yarn", "watch" ]

