# Manual backend.lylysresturant.com 

### Docker Commands & instructions to run project

1.  To run project run command: `docker compose up -d`
2.  To stop project run command: `docker compose down`
3.  To persist data & to show live changes:
    - First run docker-compose.yml without volume section with above given command
    - Now stop project
    - Uncomment volume section
    - Now again run project
4. For any seeding command run : `docker exec -it lylys-backend {your command instead of this statement here without curly brackets}`

&nbsp;