#!/usr/bin/env bash

type docker >/dev/null 2>&1 || { echo >&2 "Docker missing. Please install and run the script again."; exit 1; }
type docker-compose >/dev/null 2>&1 || { echo >&2 "Docker Compose missing. Please install and run the script again."; exit 1; }

trap '' INT

yellow=$(tput setaf 3)
greeny=$(tput setaf 2)
lila=$(tput setaf 4)
bold=$(tput bold)
reset=$(tput sgr0)

warning ()
{
    echo "  ${yellow}==>${reset}${bold} $1${reset}"
}

success ()
{
    echo "  ${greeny}==>${reset}${bold} $1${reset}"
}

info ()
{
    echo "  ${lila}==>${reset}${bold} $1${reset}"
}

exp ()
{
  if [[ "$LATEST" == "green" ]]; then
      echo "${greeny}$LATEST${reset}"
  else
      echo "${lila}$LATEST${reset}"
  fi
}

old ()
{
  if [[ "$OLD" == "green" ]]; then
      echo "${greeny}$OLD${reset}"
  else
      echo "${lila}$OLD${reset}"
  fi
}

blue=$(docker ps -f name=blue -q)
green=$(docker ps -f name=green -q)
proxy=$(docker ps -f name=traefik -q)

if [[ -z $proxy ]]; then
    info "Deploying $(echo "${lila}proxy${reset}") container"
    docker-compose --env-file prod.env -f docker-compose-proxy.yml --project-name=traefik up -d
fi

if [[ -z $blue ]] && [[ -z $green ]]; then
    info "Looks like initial setup ... deploying $(echo "${greeny}green${reset}") container"
    docker-compose --env-file prod.env -f docker-compose-prod.yml --project-name=green up -d
    while ! docker logs green_explorer_1 | grep -q "success: redis entered RUNNING state";
    do
        ( trap '' INT; exec sleep 2; )
        info "Waiting for deployment to finish ..."
    done
    success "Done! - $(echo "${greeny}green${reset}") Explorer container started!"
    info "It may take few more seconds until Explorer is fully UP ..."
    sleep 5
    success "Done!"
    exit 0
fi

if [[ -z $blue ]]; then
    LATEST="blue"
    OLD="green"
else
    LATEST="green"
    OLD="blue"
fi

info "Deploying $(exp) Explorer container ..."
docker-compose --env-file prod.env -f docker-compose-prod.yml pull
docker-compose --env-file prod.env -f docker-compose-prod.yml --project-name=$LATEST up -d

while ! docker logs "$LATEST"_explorer_1 | grep -q "cron daemon, started with loglevel notice";
do
   ( trap '' INT; exec sleep 2; )
   info "Waiting for deployment to finish ..."
done

success "Done! - $(exp) Explorer container started!"
info "Sending checks to make sure $(exp) Explorer is UP ..."

latest_ip=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' "$LATEST"_explorer_1)
latest_alive=$(curl -LI http://$latest_ip:8898 -o /dev/null -w '%{http_code}\n' -s)

sleep 5

if [[ $latest_alive -eq 200  ]]; then
    success "Done! - $(exp) Explorer container successfully deployed!"
    warning "Removing $(old) Explorer container ..."
    docker-compose --env-file prod.env -f docker-compose-prod.yml --project-name=$OLD down -v
    success "Done! - You can safely remove explorer-$(old) folder!"
else
    warning "Seems like $(exp) container is UP but Explorer was slow to respond checks - please check your logs ..."
    warning "Removing $(old) Explorer container ..."
    docker-compose --env-file prod.env -f docker-compose-prod.yml --project-name=$OLD down -v
    success "Done!"
    exit 0
fi
