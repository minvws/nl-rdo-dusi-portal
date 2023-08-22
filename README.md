### Run backend
To run the backend applications simply run:

```./run.sh -c -i -m```

To see a list of all available options run:

```./run.sh -h```

When you have 'old' dus-i containers running you may want to run: 

```
docker rm $(docker stop $(docker ps -a -q --filter="name=nl-rdo-dusi")) | echo "No containers to rm"
```
