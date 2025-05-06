import sys
import json

# Define questions for each category
questions = {
    "Money Avoidance": [
        "I do not deserve a lot of money when others have less than me.",
        "Rich people are greedy.",
        "People get rich by taking advantage of others.",
        "I do not deserve money.",
        "Good people should not care about money."
    ],
    "Money Worship": [
        "Things would get better if I had more money.",
        "More money will make you happier.",
        "It is hard to be poor and happy.",
        "You can never have enough money.",
        "Money is power."
    ],
    "Money Status": [
        "Most poor people do not deserve to have money.",
        "You can have love or money, but not both.",
        "I will not buy something unless it is new (e.g., car, house).",
        "Poor people are lazy.",
        "Money is what gives life meaning."
    ],
    "Money Vigilance": [
        "You should not tell others how much money you have or make.",
        "It is wrong to ask others how much money they have or make.",
        "Money should be saved not spent.",
        "It is important to save for a rainy day.",
        "People should work for their money and not be given financial handouts."
    ]
}

# Descriptions
descriptions = {
    "Money Avoidance": "Money Avoiders may also believe that they do not deserve money. They may believe that wealthy people are greedy or corrupt. They often believe that there is virtue in living with less money.",
    "Money Worship": "Money Worshipers believe that money is the key to happiness. They feel that the solution to their problems is to have more money. At the same time, they believe that one can never have enough money. They find that the pursuit of money never quite satisfies them.",
    "Money Status": "Money Status seekers tend to link their self-worth with their net worth. They may prioritize outward displays of wealth. This behavior can put them at risk of overspending.",
    "Money Vigilance": "The Money Vigilant are alert, watchful, and concerned about their financial health. Feeling that they have enough money is important to them. They believe it is important to save."
}

def validate_answers(answers):
    """Validate the structure and values of answers."""
    if not isinstance(answers, dict):
        return False, "Answers must be a dictionary"
    
    for category, responses in answers.items():
        if category not in questions:
            return False, f"Invalid category: {category}"
        if not isinstance(responses, list):
            return False, f"Responses for {category} must be a list"
        if len(responses) != len(questions[category]):
            return False, f"Wrong number of responses for {category}"
        for response in responses:
            if not isinstance(response, (int, float)):
                return False, f"Response must be a number in {category}"
            if not 1 <= response <= 5:
                return False, f"Response must be between 1 and 5 in {category}"
    return True, None

def calculate_scores(answers):
    """Calculate scores from answers and return results."""
    scores = {}
    for category, responses in answers.items():
        scores[category] = sum(responses)
    top_category = max(scores, key=scores.get)
    description = descriptions[top_category]
    return scores, top_category, description

def interactive_mode():
    """Run the test in interactive mode."""
    scores = {category: 0 for category in questions}
    print("Rate each statement from 1 to 5:\n1 = Strongly Disagree, 5 = Strongly Agree\n")

    for category, qs in questions.items():
        print(f"\n{category}")
        for q in qs:
            while True:
                try:
                    answer = int(input(f"{q}\nYour answer (1-5): "))
                    if 1 <= answer <= 5:
                        scores[category] += answer
                        break
                    else:
                        print("Please enter a number from 1 to 5.")
                except ValueError:
                    print("Invalid input. Please enter a number from 1 to 5.")

    print("\n--- Your Results ---")
    for cat, score in scores.items():
        print(f"{cat}: {score} points")

    top_category = max(scores, key=scores.get)
    print(f"\n🏆 Dominant Money Belief: {top_category}")
    print(descriptions[top_category])

def main():
    """Main function to handle both interactive and command-line modes."""
    if len(sys.argv) == 1:
        # Interactive mode
        interactive_mode()
        return

    if len(sys.argv) != 2:
        print(json.dumps({'status': 'error', 'message': 'Invalid number of arguments'}))
        return

    try:
        input_data = json.loads(sys.argv[1])
        email = input_data.get('email')
        answers = input_data.get('answers')

        if not email or not answers:
            print(json.dumps({'status': 'error', 'message': 'Missing email or answers'}))
            return

        # Validate answers
        is_valid, error_message = validate_answers(answers)
        if not is_valid:
            print(json.dumps({'status': 'error', 'message': error_message}))
            return

        # Calculate and return results
        scores, top_category, description = calculate_scores(answers)
        print(json.dumps({
            'status': 'ok',
            'scores': scores,
            'top_category': top_category,
            'description': description
        }))

    except json.JSONDecodeError:
        print(json.dumps({'status': 'error', 'message': 'Invalid JSON input'}))
    except Exception as e:
        print(json.dumps({'status': 'error', 'message': str(e)}))

if __name__ == '__main__':
    main()
