This is a very old project born to solve a simple problem of students house: splitting bills.

This problem has exponential complexity based on the number of students in the house and this tool
can help to understand how much money is due to who with ease.
At first glance it can seems an easy task but in a big student house with many expenses this can be a nighmare.

The principle behind the tool is simple but is better explained with an example:

Let's say that in the house there are 3 people: Alice, Bob and Jerry

1) Everyone can inser an expense of the house. Let's say that Alice insert the electricity bill of 30 Euros.
2) Conti will create the Expense ("Spesa" in italian) and two movements of 10 Euros from Bob to Alice and another one with same import Jerry to Alice.
3) Now Bob will insert the water bill of 15 Euros. Same as before, new two movements are generated: 5 euros from Alice to Bob and Jerry to Bob.
4) If someone will run the tool "Tornaconti", the program will create a logic graph to represent the debts: very people is a node and every debt is an arch with a value ("Movimento").
5) If a loop is found, it will substract the lower arc value to the loop and conseguently the loop is eliminated.
6) The result is a semplified loop-free graph that indicate how much is due by everyone to everyone else.

All of this is presented with a nice UI and a visually clear graph made with graphviz.

I'm keeping this project only for historical reasons. It's ugly and old but it helped me at the university.
Feel free to fork it or to take part of it for your projects.
